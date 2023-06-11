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
        return { path: '/' };
      }
    },
    children: [
      {
        path: '/panel',
        component: () => import('src/components/UnifiedDashboard.vue'),
      },
      {
        path: '/panel/admin',
        beforeEnter: () => {
          if (
            JSON.parse(sessionStorage.getItem('user') || '').JABATAN_PEGAWAI !=
            0
          ) {
            return { path: '/panel' };
          }
        },
        children: [
          {
            path: '/panel/admin/instructors',
            component: () =>
              import('src/components/AdminComponents/Instructors.vue'),
          },
        ],
      },
      {
        path: '/panel/operational-manager',
        beforeEnter: () => {
          if (
            JSON.parse(sessionStorage.getItem('user') || '').JABATAN_PEGAWAI !=
            1
          ) {
            return { path: '/panel' };
          }
        },
        children: [
          {
            path: '/panel/operational-manager/weekly-schedules',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/WeeklySchedules.vue'
              ),
          },
          {
            path: '/panel/operational-manager/daily-schedules',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/DailySchedules.vue'
              ),
          },
          {
            path: '/panel/operational-manager/instructor-attendances',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/InstructorAttendances.vue'
              ),
          },
          {
            path: '/panel/operational-manager/income-reports',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/IncomeReports.vue'
              ),
          },
          {
            path: '/panel/operational-manager/class-activity-reports',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/ClassActivityReports.vue'
              ),
          },
          {
            path: '/panel/operational-manager/gym-activity-reports',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/GymActivityReports.vue'
              ),
          },
          {
            path: '/panel/operational-manager/instructor-performance-reports',
            component: () =>
              import(
                'src/components/OperationaManagerComponents/InstructorPerformanceReports.vue'
              ),
          },
        ],
      },
      {
        path: '/panel/cashier',
        beforeEnter: () => {
          if (
            JSON.parse(sessionStorage.getItem('user') || '').JABATAN_PEGAWAI !=
            2
          ) {
            return { path: '/panel' };
          }
        },
        children: [
          {
            path: '/panel/cashier/members',
            component: () =>
              import('src/components/CashierComponents/Members.vue'),
          },
          {
            path: '/panel/cashier/gym-attendances',
            component: () =>
              import('src/components/CashierComponents/GymAttendances.vue'),
          },
          {
            path: '/panel/cashier/class-attendances',
            component: () =>
              import('src/components/CashierComponents/ClassAttendances.vue'),
          },
        ],
      },
    ],
  },
  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
