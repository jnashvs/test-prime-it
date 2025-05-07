<template>
    <Head title="Appointments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <el-row :gutter="20">
                <el-col :span="4">
                    <el-select v-model="selectedAnimalType" @change="loadAppointments" :disabled="loading" placeholder="Select Animal Type">
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
                        @change="loadAppointments"
                        v-model="selectedDate"
                        value-format="YYYY-MM-DD"
                        format="DD/MM/YYYY"
                        type="date"
                        placeholder="Select Date"
                    ></el-date-picker>
                </el-col>
                <el-col :span="4">
                    <el-input
                        v-model="searchQuery"
                        @input="loadAppointments"
                        placeholder="Search Appointments"
                        clearable
                        style="width: 100%;"
                    ></el-input>
                </el-col>
            </el-row>

            <el-row v-if="user && (user.user_type_id === 1 || user.user_type_id === 3)" :gutter="20">
                <el-col :span="6">
                    <el-button
                        type="primary"
                        :disabled="loading"
                        @click="openModal()"
                    >
                        Add Appointment
                    </el-button>
                </el-col>
            </el-row>

            <el-table :data="appointments" style="width: 100%">
                <el-table-column prop="date" label="Date" width="180"></el-table-column>
                <el-table-column prop="time_of_day" label="Time of Day" width="180"></el-table-column>
                <el-table-column prop="pet.name" label="Pet" width="180"></el-table-column>
                <el-table-column prop="pet.animal_type.name" label="Pet Type" width="180"></el-table-column>
                <el-table-column prop="doctor.name" label="Doctor" width="180"></el-table-column>
                <el-table-column prop="status.name" label="Status" width="180"></el-table-column>
                <el-table-column prop="symptoms" label="Symptoms" width="180"></el-table-column>
                <el-table-column label="Actions" width="180">
                    <template #default="scope">
                        <el-button
                            @click="openModal(scope.row)"
                        >
                            Edit
                        </el-button>
                        <el-button
                            v-if="user && user.user_type_id === 1"
                            type="danger"
                            @click="deleteAppointment(scope.row.id)"
                        >
                            Delete
                        </el-button>
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

            <CreateUpdateAppointment
                :isVisible="isModalVisible"
                :appointmentForm="appointmentForm"
                :animalTypes="animalTypes"
                :pets="pets"
                :doctors="doctors"
                :disabledDates="disabledDates"
                :user="user"
                @update:isVisible="isModalVisible = $event"
                @refreshAppointments="loadAppointments"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import * as animalTypeService from '@/service/api/animal-types';
import * as doctorService from '@/service/api/doctors';
import * as appointmentService from '@/service/api/appointments';
import * as petService from '@/service/api/pets';
import { ElNotification } from 'element-plus';
import CreateUpdateAppointment from '@/components/CreateUpdateAppointment.vue';
import { type SharedData, type User } from '@/types';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Appointments',
        href: '/appointments',
    },
];

const disabledDates = (date: Date) => {
    return date < new Date();
};

const loading = ref(false);
const animalTypes = ref([]);
const appointments = ref([]);
const doctors = ref([]);
const pets = ref([]);
const selectedAnimalType = ref(null);
const selectedDate = ref(null);
const searchQuery = ref();
const isModalVisible = ref(false);
const appointmentForm = ref({
    id: null,
    pet_id: null,
    doctor_id: null,
    date: null,
    time_of_day: null,
    status_id: null,
    symptoms: null,
});

const serverOptions = ref({
    page: 1,
    rowsPerPage: 20,
    sortBy: 'id',
    sortType: 'desc',
});

const params = ref({
    search: null,
    animalTypeId: null,
    date: null,
});

const totalItems = ref(0);

const loadAppointments = async () => {
    loading.value = true;
    params.value.search = searchQuery.value || '';
    params.value.animalTypeId = selectedAnimalType.value || null;
    params.value.date = selectedDate.value || null;

    try {
        const result = await appointmentService.get({
            ...params.value,
            pageIndex: serverOptions.value.page,
            pageSize: serverOptions.value.rowsPerPage,
            sortBy: serverOptions.value.sortBy,
            sortDesc: 1,
        });
        appointments.value = result?.data.data || [];
        totalItems.value = result?.data.recordsTotal || 0;
    } catch (error) {
        console.log(error);
        appointments.value = [];
        totalItems.value = 0;
    }
    loading.value = false;
};

const loadPets = async () => {
    loading.value = true;
    try {
        const result = await petService.get({
            pageIndex: 0,
            pageSize: 9999,
            sortBy: 'name',
            sortDesc: 1,
        });
        pets.value = result?.data.data || [];
    } catch (error) {
        pets.value = [];
    }
    loading.value = false;
};

const handlePageChange = (newPage) => {
    serverOptions.value.page = newPage;
    loadAppointments();
};

onMounted(() => {
    loadAnimalTypes();
    loadAppointments();
    loadDoctors();
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

const loadDoctors = async () => {
    loading.value = true;
    try {
        const result = await doctorService.get();
        if (result?.data) {
            doctors.value = result.data.data ?? [];
        } else if (result?.errors) {
            console.log('Failed to load doctros, error: ', result);
        }
    } catch (error) {
        console.log('Error loading animal types:', error);
    }
    loading.value = false;
};

const openModal = (appointment = null) => {
    if (appointment) {
        appointmentForm.value = {
            id: appointment.id,
            pet_id: appointment.pet_id,
            doctor_id: appointment.doctor_id,
            date: appointment.date,
            time_of_day: appointment.time_of_day,
            status_id: appointment.status_id,
            symptoms: appointment.symptoms,
        };
    } else {
        appointmentForm.value = {
            id: null,
            pet_id: null,
            doctor_id: null,
            date: '',
            time_of_day: '',
            status_id: null,
            symptoms: '',
        };
    }
    isModalVisible.value = true;
};

const deleteAppointment = async (id: number) => {
    if (!id) return;
    loading.value = true;
    try {
        await appointmentService.remove(id);
        ElNotification({
            title: 'Success',
            message: 'Appointment deleted successfully!',
            type: 'success',
        });
        await loadAppointments();
    } catch (error) {
        ElNotification({
            title: 'Error',
            message: 'Failed to delete appointment.',
            type: 'error',
        });
        console.log('Failed to delete appointment:', error);
    }
    loading.value = false;
};
</script>
