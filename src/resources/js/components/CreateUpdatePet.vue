<template>
    <el-dialog v-model="localVisible" append-to-body title="Pet Form">
        <el-form :model="petForm" :inline="false" label-position="top">
            <el-form-item label="Name">
                <el-input v-model="petForm.name" style="width: 100%;"></el-input>
            </el-form-item>
            <el-form-item label="Registration Number">
                <el-input v-model="petForm.registration_number" style="width: 100%;"></el-input>
            </el-form-item>
            <el-form-item label="Animal Type">
                <el-select v-model="petForm.animal_type_id" placeholder="Select Animal Type" style="width: 100%;">
                    <el-option
                        v-for="type in animalTypes"
                        :key="type.id"
                        :label="type.name"
                        :value="type.id"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="Breed">
                <el-input v-model="petForm.breed" style="width: 100%;"></el-input>
            </el-form-item>
            <el-form-item label="Date of Birth">
                <el-date-picker
                    v-model="petForm.date_of_birth"
                    value-format="YYYY-MM-DD"
                    placeholder="Date of birth"
                    format="DD/MM/YYYY"
                    type="date"
                    style="width: 100%;"
                    :disabled-date="disabledDates"
                ></el-date-picker>
            </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
            <el-button @click="closeDialog">Cancel</el-button>
            <el-button type="primary" :disabled="loading" @click="savePet">Save</el-button>
        </span>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits, watch } from 'vue';
import { ElNotification } from 'element-plus';
import * as petService from '@/service/api/pets';

const props = defineProps({
    isVisible: Boolean,
    petForm: Object,
    animalTypes: Array,
    disabledDates: Function,
});

const emit = defineEmits(['update:isVisible', 'refreshPets']);

const loading = ref(false);
const localVisible = ref(props.isVisible);

watch(() => props.isVisible, (newVal) => {
    localVisible.value = newVal;
});

watch(localVisible, (newVal) => {
    emit('update:isVisible', newVal);
});

const closeDialog = () => {
    localVisible.value = false;
};

const savePet = async () => {
    loading.value = true;
    try {
        let response;
        let successMessage;

        if (props.petForm.id) {
            response = await petService.update(props.petForm.id, props.petForm);
            successMessage = 'Pet updated successfully!';
        } else {
            response = await petService.create(props.petForm);
            successMessage = 'Pet created successfully!';
        }

        const hasValidationErrors = response.errors && Object.keys(response.errors).length > 0;
        const isErrorStatus = response.status >= 400;

        if (hasValidationErrors || isErrorStatus) {
            ElNotification({
                title: 'Error',
                message: response.message || 'Failed to save pet.',
                type: 'error',
            });
        } else {
            ElNotification({
                title: 'Success',
                message: successMessage,
                type: 'success',
            });
            emit('refreshPets');
            closeDialog();
        }
    } catch (error) {
       ElNotification({
            title: 'Error',
            message: 'An unexpected network or client-side error occurred.',
            type: 'error',
        });
        console.log('Failed to save pet (catch block):', error);
    } finally {
        loading.value = false;
    }
};
</script>
