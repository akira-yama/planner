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
import type { User, AgentTaskStatus, TaskStatusOption, Team } from '@/types';

type Props = {
    assignableUsers: User[];
    statusOptions: TaskStatusOption[];
    currentTeam?: Team;
};

defineProps<Props>();

const form = router.form('post', `/${$page.props.currentTeam?.slug}/tasks`, {
    title: '',
    description: '',
    status: 'pending' as AgentTaskStatus,
});
</script>

<template>
    <Head title="Create Task" />

    <h1 class="sr-only">Create Task</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Create Task"
            description="Add a new agent task"
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
                        placeholder="Task title"
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
                        placeholder="Task description..."
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

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Task' }}
                    </Button>
                    <Link :href="`/${$page.props.currentTeam?.slug}/tasks`">
                        Cancel
                    </Link>
                </div>
            </div>
        </Form>
    </div>
</template>