
import VueRouter from 'vue-router';
Vue.use(VueRouter)

// Configure vue-router
var router = new VueRouter({
    linkActiveClass: 'active',
    hashbang: false,
    saveScrollPosition: true,
    routes: [
        {
            path: '*',
            redirect: { name: 'dashboard' }
        },
        {
            path: '/dashboard',
            component : {
                template: '<div><template-index route="/dashboard" title="Tableau de bord" icon="pie-chart"></template-index></div>',
            },
            children: [
                {
                    name: 'dashboard',
                    path: '/',
                    component: require('./../components/app/dashboard/Index.vue')
                }
            ]
        }
    ]
});

export default router