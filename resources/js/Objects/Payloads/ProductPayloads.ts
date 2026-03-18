export type ProductFormPayload = {
    id: number; 
    name: string;
    sku: string;
    category_id: string;
    supplier_id: string;
    moved_at: string;
    unit: string;
    cost_price: string;
    selling_price: string;
    reorder_level: string;
    current_stock: string;
};

export type CreateProductPayload = {
    name: string;
    sku: string;
    category_id: number | null;
    supplier_id: number | null;
    moved_at: string;
    unit: string;
    cost_price: number;
    selling_price: number;
    reorder_level: number;
    current_stock: number;
};

export const createDefaultProductFormPayload = (): ProductFormPayload => ({
    name: '',
    sku: '',
    category_id: '',
    supplier_id: '',
    moved_at: new Date().toISOString().slice(0, 10),
    unit: 'pcs',
    cost_price: '0',
    selling_price: '0',
    reorder_level: '0',
    current_stock: '0',
    id: 0
});

export const toProductPayload = (
    form: ProductFormPayload,
): CreateProductPayload => ({
    name: form.name.trim(),
    sku: form.sku.trim(),
    moved_at: form.moved_at,
    category_id: form.category_id ? Number(form.category_id) : null,
    supplier_id: form.supplier_id ? Number(form.supplier_id) : null,
    unit: form.unit.trim(),
    cost_price: Number(form.cost_price),
    selling_price: Number(form.selling_price),
    reorder_level: Number(form.reorder_level),
    current_stock: Number(form.current_stock),
});
