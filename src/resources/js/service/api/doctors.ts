import { getData } from './api-service';

const prefix = '/doctors';

export const get = async () => {
    return await getData(prefix);
};
