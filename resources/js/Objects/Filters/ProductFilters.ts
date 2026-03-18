export type ProductFilters = {
    name: string;
    sku: string;
    category_id: string;
    supplier_id: string;
    created_after: string;
    created_before: string;
};

export const createDefaultProductFilters = (): ProductFilters => ({
    name: '',
    sku: '',
    category_id: '',
    supplier_id: '',
    created_after: '',
    created_before: '',
});
