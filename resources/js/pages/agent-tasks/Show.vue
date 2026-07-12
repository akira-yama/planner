<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Edit, Calendar, FileText } from '@lucide/vue';
import { User as UserIcon } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { AgentDoc, AgentTask, Team } from '@/types';

type Props = {
    task: AgentTask & {
        assignee?: {
            id: number;
            name: string;
            email: string;
        } | null;
    };
    docs: AgentDoc[];
    currentTeam?: Team;
};

defineProps<Props>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Tasks',
                href: `/${props.currentTeam?.slug}/tasks`,
            },
            {
                title: props.task.title,
                href: `/${props.currentTeam?.slug}/tasks/${props.task.id}`,
            },
        ],
    }),
});
</script>

<template>
    <Head :title="task.title" />

    <h1 class="sr-only">{{ task.title }}</h1>

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                :title="task.title"
                description="Task details and related documents"
            />

            <Link :href="`/${$page.props.currentTeam?.slug}/tasks/${task.id}/edit`">
                <Button variant="outline">
                    <Edit class="h-4 w-4" />
                    Edit
                </Button>
            </Link>
        </div>

        <div class="space-y-4 rounded-lg border p-6">
            <p v-if="task.description" class="text-sm">{{ task.description }}</p>

            <div class="flex items-center gap-4 text-sm text-muted-foreground">
                <span v-if="task.assignee" class="flex items-center gap-1">
                    <UserIcon class="h-3 w-3" />
                    Assigned to: {{ task.assignee.name }}
                </span>
                <span v-if="task.due_at" class="flex items-center gap-1">
                    <Calendar class="h-3 w-3" />
                    Due: {{ new Date(task.due_at).toLocaleDateString() }}
                </span>
            </div>

            <div class="pt-2">
                <Badge :variant="task.status === 'completed' ? 'default' : 'secondary'">
                    {{ task.status.replace('_', ' ') }}
                </Badge>
            </div>
        </div>

        <div v-if="docs.length > 0" class="space-y-4">
            <h2 class="text-lg font-medium">Related Documents</h2>
            <div
                v-for="doc in docs"
                :key="doc.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <Link
                        :href="`/${$page.props.currentTeam?.slug}/docs/${doc.slug}`"
                        class="font-medium hover:underline"
                    >
                        {{ doc.title }}
                    </Link>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Version {{ doc.version }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>