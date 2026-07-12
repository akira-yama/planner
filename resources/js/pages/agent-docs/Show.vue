<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Edit, Plus, Calendar, FileText } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { AgentDoc, AgentTask, Team } from '@/types';

type Props = {
    doc: AgentDoc;
    tasks: AgentTask[];
    currentTeam?: Team;
};

defineProps<Props>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Documents',
                href: `/${props.currentTeam?.slug}/docs`,
            },
            {
                title: props.doc.title,
                href: `/${props.currentTeam?.slug}/docs/${props.doc.slug}`,
            },
        ],
    }),
});
</script>

<template>
    <Head :title="doc.title" />

    <h1 class="sr-only">{{ doc.title }}</h1>

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                :title="doc.title"
                :description="`Version ${doc.version}`"
            />

            <Link :href="`/${$page.props.currentTeam?.slug}/docs/${doc.slug}/edit`">
                <Button variant="outline">
                    <Edit class="h-4 w-4" />
                    Edit
                </Button>
            </Link>
        </div>

        <div class="prose max-w-none rounded-lg border p-6">
            <pre class="whitespace-pre-wrap font-mono text-sm">{{ doc.content || 'No content yet.' }}</pre>
        </div>

        <div v-if="tasks.length > 0" class="space-y-4">
            <h2 class="text-lg font-medium">Related Tasks</h2>
            <div
                v-for="task in tasks"
                :key="task.id"
                class="flex items-center justify-between rounded-lg border p-4"
            >
                <div>
                    <Link
                        :href="`/${$page.props.currentTeam?.slug}/tasks/${task.id}`"
                        class="font-medium hover:underline"
                    >
                        {{ task.title }}
                    </Link>
                    <p v-if="task.description" class="mt-1 text-sm text-muted-foreground">
                        {{ task.description }}
                    </p>
                </div>
                <Badge :variant="task.status === 'completed' ? 'default' : 'secondary'">
                    {{ task.status }}
                </Badge>
            </div>
        </div>
    </div>
</template>