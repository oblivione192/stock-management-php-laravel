export type MovementFilters = {
    product_name: string;
    product_sku: string;
    movement_type: string;
    created_after: string;
    created_before: string;
} 

export const createDefaultMovementFilters = (): MovementFilters => ({
    product_name: '',
    product_sku: '',
    movement_type: '',
    created_after: '',
    created_before: '',
});