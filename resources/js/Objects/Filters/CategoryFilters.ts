export type CategoryFilters = {
    name: string;
    description: string;
};

export const createDefaultCategoryFilters = (): CategoryFilters => ({
    name: '',
    description: '',
});
