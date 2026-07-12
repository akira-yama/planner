<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus, Calendar, User } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { AgentTask, TaskStatusOption, Team } from '@/types';

type Props = {
    tasks: {
        data: AgentTask[];
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
    statusOptions: TaskStatusOption[];
    currentTeam?: Team;
};

const props = defineProps<Props>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Tasks',
                href: `/${props.currentTeam?.slug}/tasks`,
            },
        ],
    }),
});
</script>

<template>
    <Head title="Tasks" />

    <h1 class="sr-only">Tasks</h1>

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Tasks"
                description="Manage your agent tasks"
            />

            <Link :href="`/${$page.props.currentTeam?.slug}/tasks/create`">
                <Button>
                    <Plus class="h-4 w-4" />
                    New Task
                </Button>
            </Link>
        </div>

        <div v-if="props.tasks.data.length === 0" class="py-8 text-center text-muted-foreground">
            No tasks found. Create your first task to get started.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="task in props.tasks.data"
                :key="task.id"
                class="flex items-start justify-between gap-4 rounded-lg border p-4"
            >
                <div class="flex-1">
                    <Link
                        :href="`/${$page.props.currentTeam?.slug}/tasks/${task.id}`"
                        class="font-medium hover:underline"
                    >
                        {{ task.title }}
                    </Link>

                    <p v-if="task.description" class="mt-1 text-sm text-muted-foreground line-clamp-2">
                        {{ task.description }}
                    </p>

                    <div class="mt-2 flex items-center gap-4 text-sm text-muted-foreground">
                        <span v-if="task.assignee" class="flex items-center gap-1">
                            <User class="h-3 w-3" />
                            {{ task.assignee.name }}
                        </span>
                        <span v-if="task.due_at" class="flex items-center gap-1">
                            <Calendar class="h-3 w-3" />
                            Due {{ new Date(task.due_at).toLocaleDateString() }}
                        </span>
                    </div>
                </div>

                <Badge :variant="task.status === 'completed' ? 'default' : 'secondary'">
                    {{ task.status.replace('_', ' ') }}
                </Badge>
            </div>
        </div>
    </div>
</template>