export type CategoryRow = {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
};

export type CategoriesResponse = {
    data: CategoryRow[];
};
