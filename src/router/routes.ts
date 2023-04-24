import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('pages/IndexPage.vue'),
  },
  {
    path: '/panel',
    component: () => import('layouts/UnifiedLayout.vue'),
    beforeEnter: () => {
      if (sessionStorage.getItem('user') == null) {
        return { path: '/' }
      }
    },
    children: [
      {
        path: '/panel/administrator',
        beforeEnter: () => {
          if (JSON.parse(sessionStorage.getItem('user') || '').JABATAN_PEGAWAI != 0) {
            return { path: '/panel' }
          }
        },
        children: [
          {
            path: '/panel/admin/instructors',
            component: () => import('src/components/AdminComponents/Instructors.vue'),
          }
        ]
      },
      {
        path: '/panel/operational-manager',
        beforeEnter: () => {
          if (JSON.parse(sessionStorage.getItem('user') || '').JABATAN_PEGAWAI != 1) {
            return { path: '/panel' }
          }
        },
        children: [
          {
            path: '/panel/operational-manager/weekly-schedules',
            component: () => import('src/components/OperationaManagerComponents/WeeklySchedules.vue'),
          }
        ]
      },
      {
        path: '/panel/cashier',
        beforeEnter: () => {
          if (JSON.parse(sessionStorage.getItem('user') || '').JABATAN_PEGAWAI != 2) {
            return { path: '/' }
          }
        },
        children: [
          {
            path: '/panel/cashier/members',
            component: () => import('src/components/CashierComponents/Members.vue'),
          }
        ]
      }
    ]
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
