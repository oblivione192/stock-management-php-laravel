export type Metrics = {
    products: number;
    categories: number;
    suppliers: number;
    totalUnits: number;
    lowStockProducts: number;
    totalStockValue: number;
};

export type LowStockProduct = {
    id: number;
    sku: string;
    name: string;
    current_stock: number;
    reorder_level: number;
    category: {
        name: string;
    } | null;
};

export type RecentMovement = {
    id: number;
    movement_type: 'stock_in' | 'stock_out' | 'adjustment';
    quantity: number;
    reference: string | null;
    product: {
        name: string;
        sku: string;
    } | null;
    user: {
        name: string;
    } | null;
};

export type DashboardResponse = {
    metrics: Metrics;
    lowStockProducts: LowStockProduct[];
    recentMovements: RecentMovement[];
};
