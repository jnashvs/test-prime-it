<template>
    <Head title="Pets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <el-row :gutter="20">
                <el-col :span="4">
                    <el-select v-model="selectedAnimalType" @change="loadPets" :disabled="loading" placeholder="Select Animal Type">
                        <el-option value="">All</el-option>
                        <el-option
                            v-for="type in animalTypes"
                            :key="type.id"
                            :label="type.name"
                            :value="type.id"
                        ></el-option>
                    </el-select>
                </el-col>
                <el-col :span="4">
                    <el-date-picker
                        @change="loadPets"
                        v-model="selectedDateRange"
                        type="daterange"
                        range-separator="To"
                        start-placeholder="Start date"
                        end-placeholder="End date"
                    ></el-date-picker>
                </el-col>
            </el-row>

            <el-row :gutter="20">
                <el-col :span="11">
                    <el-input
                        v-model="searchQuery"
                        @input="loadPets"
                        placeholder="Search Pets"
                        clearable
                        style="width: 100%;"
                    ></el-input>
                </el-col>
            </el-row>

            <el-row :gutter="20">
                <el-col :span="12">
                    <el-button type="primary" :disabled="loading" @click="openModal()">Add Pet</el-button>
                </el-col>
            </el-row>

            <el-table :data="pets" style="width: 100%">
                <el-table-column prop="updated_at" label="Last Update" width="180"></el-table-column>
                <el-table-column prop="name" label="Name" width="180"></el-table-column>
                <el-table-column prop="registration_number" label="Registration Number" width="180"></el-table-column>
                <el-table-column prop="animal_type.name" label="Animal Type" width="180"></el-table-column>
                <el-table-column prop="owner.name" label="Owner" width="180"></el-table-column>
                <el-table-column prop="date_of_birth" label="Date of Birth" width="180"></el-table-column>
                <el-table-column label="Actions" width="180">
                    <template #default="scope">
                        <el-button @click="openModal(scope.row)">Edit</el-button>
                        <el-button type="danger" @click="deletePet(scope.row.id)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <el-pagination
                v-if="totalItems > 0"
                v-model:current-page="serverOptions.page"
                :page-size="serverOptions.rowsPerPage"
                :total="totalItems"
                layout="prev, pager, next"
                @current-change="handlePageChange"
            ></el-pagination>

            <CreateUpdatePet
                :isVisible="isModalVisible"
                :petForm="petForm"
                :animalTypes="animalTypes"
                :disabledDates="disabledDates"
                @update:isVisible="isModalVisible = $event"
                @refreshPets="loadPets"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import * as animalTypeService from '@/service/api/animal-types';
import * as petService from '@/service/api/pets';
import { ElNotification } from 'element-plus';
import CreateUpdatePet from '@/components/CreateUpdatePet.vue';

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

const disabledDates = (date: Date) => {
    return date > new Date();
};

const loading = ref(false);
const animalTypes = ref([]);
const pets = ref([]);
const selectedAnimalType = ref(null);
const selectedDateRange = ref([]);
const searchQuery = ref();
const isModalVisible = ref(false);
const petForm = ref({
    id: null,
    name: '',
    registration_number: '',
    animal_type_id: null,
    breed: '',
    date_of_birth: '',
});

const serverOptions = ref({
    page: 1,
    rowsPerPage: 20,
    sortBy: 'updated_at',
    sortType: 'desc',
});

const params = ref({
    search: null,
    typeId: null,
    dateFrom: null,
    dateTo: null,
});

const totalItems = ref(0);

const loadPets = async () => {
    loading.value = true;
    params.value.search = searchQuery.value || '';
    params.value.typeId = selectedAnimalType.value || null;
    params.value.dateFrom = selectedDateRange.value[0] || null;
    params.value.dateTo = selectedDateRange.value[1] || null;

    try {
        const result = await petService.get({
            ...params.value,
            pageIndex: serverOptions.value.page,
            pageSize: serverOptions.value.rowsPerPage,
            sortBy: serverOptions.value.sortBy,
            sortDesc: 1,
        });
        pets.value = result?.data.data || [];
        totalItems.value = result?.data.recordsTotal || 0;
    } catch (error) {
        console.log(error);
        pets.value = [];
        totalItems.value = 0;
    }
    loading.value = false;
};

const handlePageChange = (newPage) => {
    serverOptions.value.page = newPage;
    loadPets();
};

onMounted(() => {
    loadAnimalTypes();
    loadPets();
});

const loadAnimalTypes = async () => {
    loading.value = true;
    try {
        const result = await animalTypeService.get();
        if (result?.data) {
            animalTypes.value = result.data.data ?? [];
        } else if (result?.errors) {
            console.log('Failed to load animal types, error: ', result);
        }
    } catch (error) {
        console.log('Error loading animal types:', error);
    }
    loading.value = false;
};

const openModal = (pet = null) => {
    if (pet) {
        petForm.value = {
            id: pet.id,
            name: pet.name,
            registration_number: pet.registration_number,
            animal_type_id: pet.animal_type_id,
            breed: pet.breed,
            date_of_birth: pet.date_of_birth,
        };
    } else {
        petForm.value = {
            id: null,
            name: '',
            registration_number: '',
            animal_type_id: null,
            breed: '',
            date_of_birth: '',
        };
    }
    isModalVisible.value = true;
};

const deletePet = async (id: number) => {
    if (!id) return;
    loading.value = true;
    try {
        const response = await petService.remove(id);
        const hasValidationErrors = response.errors && Object.keys(response.errors).length > 0;
        const isErrorStatus = response.status >= 400;

        if (hasValidationErrors || isErrorStatus) {
            ElNotification({
                title: 'Error',
                message: response.message ?? 'Failed to delete pet.',
                type: 'error',
            });
        } else {
            ElNotification({
                title: 'Success',
                message: 'Pet deleted successfully!',
                type: 'success',
            });

            await loadPets();
        }

    } catch (error) {
        ElNotification({
            title: 'Error',
            message: 'Failed to delete pet.',
            type: 'error',
        });
        console.log('Failed to delete pet:', error);
    } finally {
        loading.value = false;
    }
};
</script>
