<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { AgentDoc, Team } from '@/types';

type Props = {
    doc: AgentDoc;
    currentTeam?: Team;
};

const props = defineProps<Props>();

const form = router.form('put', `/${props.currentTeam?.slug}/docs/${props.doc.slug}`, {
    title: props.doc.title,
    content: props.doc.content,
    version: props.doc.version,
});
</script>

<template>
    <Head title="Edit Document" />

    <h1 class="sr-only">Edit Document</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Edit Document"
            description="Update your agent document"
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
                    <Label for="content">Content (Markdown)</Label>
                    <textarea
                        id="content"
                        v-model="form.content"
                        rows="10"
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    />
                    <InputError :message="form.errors.content" />
                </div>

                <div class="grid gap-2">
                    <Label for="version">Version</Label>
                    <Input
                        id="version"
                        v-model="form.version"
                        type="text"
                    />
                    <InputError :message="form.errors.version" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                    <Link :href="`/${$page.props.currentTeam?.slug}/docs`">
                        Cancel
                    </Link>
                </div>
            </div>
        </Form>
    </div>
</template>