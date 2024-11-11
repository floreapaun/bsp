<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostForm from '@/Components/PostForm.vue';
import { Head } from '@inertiajs/vue3';
import PostsIndex from '@/Components/PostsIndex.vue';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="py-6 text-gray-900">
                        <PostForm/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
