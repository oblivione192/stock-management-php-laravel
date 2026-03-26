export type CategoryOption = {
    id: number;
    name: string;
};

export type ProductRow = {
    id: number;
    sku: string;
    name: string;
    moved_at: string;
    category_id: number | null;
    supplier_id: number | null;
    unit: string;
    cost_price: number;
    selling_price: number;
    reorder_level: number;
    current_stock: number;
    category: {
        name: string;
    } | null;
    supplier: {
        name: string;
    } | null;
};

export type ProductDeleteResult = {
    id: number;
    loss: {
        current_stock: number;
        cost_price: number;

    };
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
