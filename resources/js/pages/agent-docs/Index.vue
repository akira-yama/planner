<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import type { AgentDoc, Team } from '@/types';

type Props = {
    docs: {
        data: AgentDoc[];
        links?: Array<{ url: string | null; label: string; active: boolean }>;
    };
    currentTeam?: Team;
};

const props = defineProps<Props>();

defineOptions({
    layout: (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Documents',
                href: props.currentTeam
                    ? `/${props.currentTeam.slug}/docs`
                    : '/docs',
            },
        ],
    }),
});
</script>

<template>
    <Head title="Documents" />

    <h1 class="sr-only">Documents</h1>

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Documents"
                description="Manage your agent documentation"
            />

            <Link :href="`/${$page.props.currentTeam?.slug}/docs/create`">
                <Button>
                    <Plus class="h-4 w-4" />
                    New Document
                </Button>
            </Link>
        </div>

        <div v-if="props.docs.data.length === 0" class="py-8 text-center text-muted-foreground">
            No documents found. Create your first document to get started.
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="doc in props.docs.data"
                :key="doc.id"
                class="flex items-start justify-between gap-4 rounded-lg border p-4"
            >
                <div class="flex-1">
                    <Link
                        :href="`/${$page.props.currentTeam?.slug}/docs/${doc.slug}`"
                        class="font-medium hover:underline"
                    >
                        {{ doc.title }}
                    </Link>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Version {{ doc.version }}
                    </p>
                    <p v-if="doc.content" class="mt-2 text-sm line-clamp-2">
                        {{ doc.content }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Link :href="`/${$page.props.currentTeam?.slug}/docs/${doc.slug}/edit`">
                        <Button variant="ghost" size="sm">Edit</Button>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>