import { getData, postData, putData, deleteData } from './api-service';

const prefix = '/appointments';

export const get = async (params = null) => {
    return await getData(prefix, params);
};

export const create = async (data: any) => {
    return await postData(prefix, data);
};

export const update = async (id: number, data: any) => {
    return await putData(`${prefix}/${id}`, data);
};

export const remove = async (id: number) => {
    return await deleteData(`${prefix}/${id}`);
};
