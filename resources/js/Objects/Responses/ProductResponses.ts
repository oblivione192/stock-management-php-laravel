export type CategoryOption = {
    id: number;
    name: string;
};

export type ProductRow = {
    id: number;
    sku: string;
    name: string;
    moved_at: string;
    quantity: number;
    category_id: number | null;
    supplier_id: number | null;
    unit: string;
    cost_price: string | number;
    selling_price: string | number;
    reorder_level: number;
    current_stock: number;
    category: {
        name: string;
    } | null;
    supplier: {
        name: string;
    } | null;
};

export type PaginatedProducts = {
    data: ProductRow[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
};

export type ProductsResponse = {
    products: PaginatedProducts;
};

export type ProductCategoriesResponse = {
    data: CategoryOption[];
};
