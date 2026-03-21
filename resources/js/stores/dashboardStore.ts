import type { _GettersTree } from 'pinia';
import { defineStore } from 'pinia';
import type { DashboardResponse } from '@/Objects/Responses/DashboardResponse';
import type {ProductRow} from '@/Objects/Responses/ProductResponses';

interface State{
    dashboardData: DashboardResponse,
    hasFetched: boolean,
}

interface Getters extends _GettersTree<State>{
    getHasFetched(state: State): boolean
}

const useDashboardStore = defineStore('dashboard',
    {
        state:(): State => ({
            dashboardData:  {
                metrics:{
                    products: 0,
                    categories: 0,
                    suppliers: 0,
                    totalUnits: 0,
                    lowStockProducts: 0,
                    totalStockValue: 0,
                },
                lowStockProducts:[],
                recentMovements: []
            },
            hasFetched: false
        }),
        getters:{
            getHasFetched(state: State): boolean{
                return state.hasFetched;
            }
        } as Getters,
        actions: {
             setDashboardData(dashboardResponse: DashboardResponse){
                 this.dashboardData = dashboardResponse;
             },
             clearDashboard(){
                 this.$reset();
             },
             setHasFetched(){
                 this.hasFetched = true;
             },
             handleProductAdded(product: ProductRow) {
                this.dashboardData.metrics.products += 1;
                this.dashboardData.metrics.totalUnits += product.current_stock;
                this.dashboardData.metrics.totalStockValue += product.current_stock * Number(product.cost_price);
             },
        }
    }
    );
export default useDashboardStore;
