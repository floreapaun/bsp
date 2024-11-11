<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PostsIndexAdmin from '@/Components/PostsIndexAdmin.vue';
import AuthenticatedLayoutAdmin from '@/Layouts/AuthenticatedLayoutAdmin.vue';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayoutAdmin>
        <template #header>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="py-6 text-gray-900">
                        <PostsIndexAdmin/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayoutAdmin>
</template>
