<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AgentTask, AgentTaskStatus, User, TaskStatusOption, Team } from '@/types';

type Props = {
    task: AgentTask & {
        assignee?: User | null;
    };
    assignableUsers: User[];
    statusOptions: TaskStatusOption[];
    currentTeam?: Team;
};

const props = defineProps<Props>();

const form = router.form('put', `/${props.currentTeam?.slug}/tasks/${props.task.id}`, {
    title: props.task.title ?? '',
    description: props.task.description ?? '',
    status: props.task.status as AgentTaskStatus,
    assigned_to: props.task.assignee?.id ?? null,
    due_at: props.task.due_at ? new Date(props.task.due_at).toISOString().slice(0, 16) : null,
});
</script>

<template>
    <Head title="Edit Task" />

    <h1 class="sr-only">Edit Task</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Edit Task"
            description="Update your agent task"
        />

        <Form
            :data="form"
            :errors="form.errors"
            :processing="form.processing"
            :was-successful="form.wasSuccessful"
            @submit.prevent="form.submit"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        type="text"
                        required
                        autocomplete="off"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <Select v-model="form.status">
                        <SelectTrigger>
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status" />
                </div>

                <div class="grid gap-2">
                    <Label for="assigned_to">Assignee (optional)</Label>
                    <Select v-model="form.assigned_to">
                        <SelectTrigger>
                            <SelectValue placeholder="Unassigned" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">Unassigned</SelectItem>
                            <SelectItem
                                v-for="user in assignableUsers"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.assigned_to" />
                </div>

                <div class="grid gap-2">
                    <Label for="due_at">Due Date (optional)</Label>
                    <Input
                        id="due_at"
                        v-model="form.due_at"
                        type="datetime-local"
                    />
                    <InputError :message="form.errors.due_at" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                    <Link :href="`/${$page.props.currentTeam?.slug}/tasks`">
                        Cancel
                    </Link>
                </div>
            </div>
        </Form>
    </div>
</template>