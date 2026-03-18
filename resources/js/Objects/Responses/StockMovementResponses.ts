export type MovementRow = {
    id: number;
    movement_type: 'stock_in' | 'stock_out' | 'adjustment';
    quantity: number;
    reference: string | null;
    moved_at: string;
    product: {
        name: string;
        sku: string;
    } | null;
    user: {
        name: string;
    } | null;
};

export type PaginatedMovements = {
    data: MovementRow[];
    current_page: number;
    last_page: number;
    total: number;
};

export type MovementsResponse = {
    movements: PaginatedMovements;
};
