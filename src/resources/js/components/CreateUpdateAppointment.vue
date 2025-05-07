<template>
    <el-dialog v-model="localVisible" append-to-body title="Appointment Form">
        <el-form :model="appointmentForm" :inline="false" label-position="top">
            <el-form-item label="Pet">
                <el-select v-model="appointmentForm.pet_id" placeholder="Select Pet" style="width: 100%;">
                    <el-option
                        v-for="pet in pets"
                        :key="pet.id"
                        :label="pet.name"
                        :value="pet.id"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item v-if="props.user && props.user.user_type_id === 1" label="Doctor">
                <el-select v-model="appointmentForm.doctor_id" placeholder="Select Doctor" style="width: 100%;">
                    <el-option
                        v-for="doctor in doctors"
                        :key="doctor.id"
                        :label="doctor.name"
                        :value="doctor.id"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="Date">
                <el-date-picker
                    v-model="appointmentForm.date"
                    value-format="YYYY-MM-DD"
                    placeholder="Select Date"
                    format="DD/MM/YYYY"
                    type="date"
                    style="width: 100%;"
                    :disabled-date="disabledDates"
                ></el-date-picker>
            </el-form-item>
            <el-form-item label="Time of Day">
                <el-select v-model="appointmentForm.time_of_day" placeholder="Select Time of Day" style="width: 100%;">
                    <el-option label="Morning" value="morning"></el-option>
                    <el-option label="Afternoon" value="afternoon"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item v-if="props.user && (props.user.user_type_id === 1 || props.user.user_type_id === 2)" label="Status">
                <el-select v-model="appointmentForm.status_id" placeholder="Select Status" style="width: 100%;">
                    <el-option
                        v-for="status in availableStatuses"
                        :key="status.value"
                        :label="status.label"
                        :value="status.value"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="Symptoms">
                <el-input v-model="appointmentForm.symptoms" type="textarea" style="width: 100%;"></el-input>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button @click="closeDialog">Cancel</el-button>
            <el-button type="primary" :disabled="loading" @click="saveAppointment">Save</el-button>
        </span>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits, watch, computed } from 'vue';
import { ElNotification } from 'element-plus';
import * as appointmentService from '@/service/api/appointments';
import { type User } from '@/types';

const props = defineProps({
    isVisible: Boolean,
    appointmentForm: Object,
    pets: Array,
    doctors: Array,
    disabledDates: Function,
    user: {
        type: Object as () => User,
        required: true
    }
});

const emit = defineEmits(['update:isVisible', 'refreshAppointments']);

const loading = ref(false);
const localVisible = ref(props.isVisible);

const allStatuses = [
    { label: "Requested", value: 1 },
    { label: "Pending Assignment", value: 2 },
    { label: "Assigned", value: 3 },
    { label: "Completed", value: 4 },
    { label: "Cancelled", value: 5 },
];

// user_type_id === 1 -> receptionist
// user_type_id === 2 -> doctor
const availableStatuses = computed(() => {
    if (props.user && props.user.user_type_id === 1) {
        return allStatuses;
    }
    if (props.user && props.user.user_type_id === 2) {
        return allStatuses.filter(status => status.value === 4 || status.value === 5);
    }
    return [];
});

watch(() => props.isVisible, (newVal) => {
    localVisible.value = newVal;
});

watch(localVisible, (newVal) => {
    emit('update:isVisible', newVal);
});

const closeDialog = () => {
    localVisible.value = false;
};

const saveAppointment = async () => {
    loading.value = true;
    try {
        if (props.appointmentForm.id) {
            await appointmentService.update(props.appointmentForm.id, props.appointmentForm);
            ElNotification({
                title: 'Success',
                message: 'Appointment updated successfully!',
                type: 'success',
            });
        } else {
            await appointmentService.create(props.appointmentForm);
            ElNotification({
                title: 'Success',
                message: 'Appointment created successfully!',
                type: 'success',
            });
        }
        emit('refreshAppointments');
        closeDialog();
    } catch (error) {
        ElNotification({
            title: 'Error',
            message: 'Failed to save appointment.',
            type: 'error',
        });
        console.log('Failed to save appointment:', error);
    }
    loading.value = false;
};
</script>
