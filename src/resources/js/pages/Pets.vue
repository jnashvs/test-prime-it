<template>
    <Head title="Pets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div>
                {{animalTypes}}
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import * as animalTypeService from '@/service/api/animal-types';

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Pets',
        href: '/pets',
    },
];

const loading = ref(false);
const animalTypes = ref([]);

const loadAnimalTypes = async () => {
    loading.value = true;
    try {
        const result = await animalTypeService.get();
        if (result?.data) {
            animalTypes.value = result.data;
        } else if (result?.errors) {
            console.log('Failed to load animal types, error: ', result);
        }
    } catch (error) {
        console.log(error);
    }
    loading.value = false;
};

onMounted(() => {
    loadAnimalTypes();
});
</script>
