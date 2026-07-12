<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Team } from '@/types';

type Props = {
    currentTeam?: Team;
};

defineProps<Props>();
</script>

<template>
    <Head title="Create Document" />

    <h1 class="sr-only">Create Document</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Create Document"
            description="Add a new agent document"
        />

        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    type="text"
                    required
                    autocomplete="off"
                    placeholder="Document title"
                />
            </div>

            <div class="grid gap-2">
                <Label for="content">Content (Markdown)</Label>
                <textarea
                    id="content"
                    name="content"
                    rows="10"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    placeholder="# Document content..."
                />
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit">Create Document</Button>
                <Link :href="`/${$page.props.currentTeam?.slug}/docs`">Cancel</Link>
            </div>
        </div>
    </div>
</template>