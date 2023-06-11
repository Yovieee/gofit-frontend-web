<template>
  <div class="fixed-center">
    <div class="text-h4">Welcome, {{ user.NAMA_USER }}!</div>
    <div class="text-overline q-mb-md">What do you want to do today?</div>
    <div class="row">
      <div
        class="col-auto q-mx-md q-mb-md"
        v-for="(menu, index) in menus"
        :key="index"
      >
        <q-btn :to="menu.to" :icon="menu.icon" size="xl">
          <q-tooltip>
            {{ menu.label }}
          </q-tooltip>
        </q-btn>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'UnifiedDashboardComponent',
  setup() {
    const user = ref(JSON.parse(sessionStorage.getItem('user') || ''));
    return {
      menus: ref(
        user.value.JABATAN_PEGAWAI == 0
          ? [
              {
                label: 'Instructors',
                icon: 'self_improvement',
                to: '/panel/admin/instructors',
                child: [],
              },
            ]
          : user.value.JABATAN_PEGAWAI == 1
          ? [
              {
                label: 'Weekly Schedules',
                icon: 'date_range',
                to: '/panel/operational-manager/weekly-schedules',
                child: [],
              },
              {
                label: 'Daily Schedules',
                icon: 'today',
                to: '/panel/operational-manager/daily-schedules',
                child: [],
              },
              {
                label: 'Instructor Attendances',
                icon: 'groups',
                to: '/panel/operational-manager/instructor-attendances',
                child: [],
              },
              {
                label: 'Income Reports',
                icon: 'insights',
                to: '/panel/operational-manager/income-reports',
                child: [],
              },
              {
                label: 'Class Activity Reports',
                icon: 'sports_kabaddi',
                to: '/panel/operational-manager/class-activity-reports',
                child: [],
              },
              {
                label: 'Gym Activity Reports',
                icon: 'fitness_center',
                to: '/panel/operational-manager/gym-activity-reports',
                child: [],
              },
              {
                label: 'Instructor Performance Reports',
                icon: 'sports',
                to: '/panel/operational-manager/instructor-performance-reports',
                child: [],
              },
            ]
          : [
              {
                label: 'Members',
                icon: 'group',
                to: '/panel/cashier/members',
                child: [],
              },
              {
                label: 'Gym Attendances',
                icon: 'done_all',
                to: '/panel/cashier/gym-attendances',
                child: [],
              },
              {
                label: 'Class Attendances',
                icon: 'rule',
                to: '/panel/cashier/class-attendances',
                child: [],
              },
            ]
      ),
      user,
    };
  },
});
</script>
