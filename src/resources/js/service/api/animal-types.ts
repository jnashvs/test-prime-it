import { getData } from './api-service';

const prefix = '/animal-types';

export const get = async (params = null) => {
    return await getData(prefix, params);
};
