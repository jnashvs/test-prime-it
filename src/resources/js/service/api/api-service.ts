import axios, { AxiosRequestConfig, AxiosResponse } from '@/http-axios';
import { isAxiosError } from 'axios';

interface ApiResponse<T> {
    data: T;
    errors?: Record<string, any>;
    message?: string;
    status: number;
}

const handleResponse = async <T>(request: Promise<AxiosResponse<T>>): Promise<ApiResponse<T>> => {
    try {
        const response = await request;
        return { data: response.data, status: response.status };
    } catch (error: any) {
        if (isAxiosError(error)) {
            if (error.response?.status === 401 || error.response?.status === 422 || error.response?.status === 403) {
                return {
                    data: {} as T,
                    errors: error.response.data.errors || {},
                    message: error.response.data.message || 'Validation error occurred',
                    status: error.response.status
                };
            } else if (error.code === 'ERR_CANCELED') {
                return { data: {} as T, status: -1 };
            } else {
                return {
                    data: {} as T,
                    status: error.response?.status ?? -1,
                    errors: error.response?.data?.errors || {},
                    message: error.response?.data?.message || error.message || 'An unexpected error occurred'
                };
            }
        }
        return { data: {} as T, status: -1, message: 'An unexpected error occurred' };
    }
};

const multipartHeader = { headers: { 'Content-Type': 'multipart/form-data' } };

function mergeHeaders(defaultHeaders: AxiosRequestConfig['headers'], optionsHeaders: AxiosRequestConfig['headers']) {
    return { ...defaultHeaders, ...(optionsHeaders || {}) };
}

export const getData = <T>(path: string, params: Record<string, any> | null = null, responseType: AxiosRequestConfig['responseType'] = 'json') =>
    handleResponse<T>(axios.get('/api/v1' + path, { params, responseType }));

export const postData = <T>(path: string, data: any, isMultipart = true, options: AxiosRequestConfig = {}) =>
    handleResponse<T>(
        axios.post('/api/v1' + path, data, isMultipart
            ? mergeHeaders(multipartHeader, options.headers)
            : { headers: options.headers, ...options })
    );

export const putData = <T>(path: string, data: any, isMultipart = true, options: AxiosRequestConfig = {}) =>
    handleResponse<T>(
        axios.put('/api/v1' + path, data, {
            headers: isMultipart ? mergeHeaders(multipartHeader, options.headers) : options.headers,
            ...options
        })
    );

export const deleteData = <T>(path: string) => handleResponse<T>(axios.delete('/api/v1' + path));
