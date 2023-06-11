<template>
  <q-layout v-if="$q.platform.is.desktop">
    <q-header reveal elevated class="bg-primary text-white">
      <q-toolbar class="row">
        <q-btn
          v-if="router.currentRoute.value.path != '/panel'"
          dense
          flat
          round
          :icon="leftDrawerOpen ? 'arrow_back' : 'menu'"
          @click="toggleLeftDrawer"
        />
        <q-toolbar-title class="column">
          <q-img
            src="src/assets/gofit-logo.webp"
            fit="scale-down"
            height="28px"
          />
        </q-toolbar-title>
        <div class="column">
          <q-btn round padding="none">
            <q-avatar>
              <q-icon v-if="user.FOTO_USER == null" name="person" />
              <img
                v-if="user.FOTO_USER != null"
                :src="'data:image/jpeg;base64,' + user.FOTO_USER"
              />
            </q-avatar>
            <q-menu transition-show="scale" transition-hide="scale">
              <q-item class="text-center q-mt-xs text-h6" dense>
                <q-item-section class="text-no-wrap">{{
                  user.NAMA_USER
                }}</q-item-section>
              </q-item>
              <q-item class="text-center text-overline" dense>
                <q-item-section class="text-no-wrap">{{
                  user.JABATAN_PEGAWAI == 0
                    ? 'ADMINISTRATOR'
                    : user.JABATAN_PEGAWAI == 1
                    ? 'OPERATIONAL MANAGER'
                    : 'CASHIER'
                }}</q-item-section>
              </q-item>
              <q-separator />
              <q-item clickable>
                <q-item-section @click="settingsDialog = true"
                  >Settings</q-item-section
                >
              </q-item>
              <q-item clickable>
                <q-item-section
                  @click="
                    $q.dialog({
                      title: 'Confirm',
                      message: 'Are you sure you want to logout?',
                      cancel: true,
                      persistent: true,
                    }).onOk(() => {
                      logout();
                    })
                  "
                >
                  Logout</q-item-section
                >
              </q-item>
            </q-menu>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>
    <q-drawer
      v-if="router.currentRoute.value.path != '/panel'"
      mini-to-overlay
      elevated
      show-if-above
      v-model="leftDrawerOpen"
      side="left"
      bordered
      :mini="miniState"
      @mouseover="miniState = false"
      @mouseout="miniState = true"
    >
      <q-scroll-area class="fit">
        <q-list v-for="(menu, index) in menus" :key="index">
          <q-item
            v-if="menu.child.length == 0"
            clickable
            v-ripple
            :to="menu.to"
          >
            <q-item-section avatar>
              <q-icon :name="menu.icon" />
            </q-item-section>
            <q-item-section>
              {{ menu.label }}
            </q-item-section>
          </q-item>
          <q-expansion-item
            v-if="menu.child.length != 0"
            :icon="menu.icon"
            :label="menu.label"
          >
            <q-list v-for="(child, index) in menu.child" :key="index">
              <q-item :inset-level="1" clickable v-ripple :to="child.to">
                <q-item-section avatar>
                  <q-icon :name="child.icon" />
                </q-item-section>
                <q-item-section>
                  {{ child.label }}
                </q-item-section>
              </q-item>
            </q-list>
          </q-expansion-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>
    <q-dialog v-model="settingsDialog" persistent>
      <q-card style="width: 12cm">
        <q-card-section>
          <div class="row">
            <div class="text-h6">Settings</div>
            <q-space />
            <div>
              <q-icon
                class="cursor-pointer q-mt-xs"
                size="sm"
                name="close"
                @click="settingsDialog = false"
              />
            </div>
          </div>
        </q-card-section>
        <q-card-section>
          <q-list>
            <q-item>
              <q-item-section>
                <div class="row">
                  <div class="col-4">
                    <div class="q-mt-sm">Theme</div>
                  </div>
                  <div class="col">
                    <div class="q-ml-sm">
                      <q-toggle
                        v-model="darkMode"
                        :icon="darkMode ? 'dark_mode' : 'light_mode'"
                        @click="switchToDarkMode"
                      />
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-4">
                    <div class="q-mt-sm">Password</div>
                  </div>
                  <div class="col">
                    <div class="q-ml-xs q-mt-xs">
                      <div
                        v-if="changePasswordLoadingState"
                        class="text-center"
                      >
                        <q-spinner />
                      </div>
                      <q-expansion-item
                        v-if="!changePasswordLoadingState"
                        dense
                        expand-separator
                        label="Change Now"
                      >
                        <q-form
                          @submit="
                            $q.dialog({
                              title: 'Confirm',
                              message:
                                'Are you sure you want to continue changing password?',
                              cancel: true,
                              persistent: true,
                            }).onOk(() => {
                              changePasswordProcess();
                            })
                          "
                          class="q-mt-sm"
                        >
                          <q-input
                            :rules="[
                              (val) =>
                                val.length > 0 ||
                                'Please enter current password',
                            ]"
                            dense
                            outlined
                            v-model="changePasswordValue.current.value"
                            type="password"
                            label="Current Password"
                          />
                          <q-input
                            :rules="[
                              (val) =>
                                val.length > 0 || 'Please enter new password',
                            ]"
                            dense
                            outlined
                            v-model="changePasswordValue.new.value"
                            type="password"
                            label="New Password"
                          />
                          <q-input
                            :rules="[
                              (val) =>
                                val == changePasswordValue.new.value ||
                                'Password does not match!',
                            ]"
                            dense
                            outlined
                            v-model="changePasswordValue.retype.value"
                            type="password"
                            label="Retype New Password"
                          />
                          <q-btn
                            class="float-right"
                            type="submit"
                            color="primary"
                            icon="check"
                          />
                        </q-form>
                      </q-expansion-item>
                    </div>
                  </div>
                </div>
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-page-container>
      <q-page padding>
        <router-view />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { ref } from 'vue';
import { useQuasar } from 'quasar';
import { api } from 'src/boot/axios';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const router = useRouter();
    const $q = useQuasar();
    const leftDrawerOpen = ref(false);
    const user = ref(JSON.parse(sessionStorage.getItem('user')));
    const menus = ref(
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
    );
    const miniState = ref(true);
    const settingsDialog = ref(false);
    const darkMode = ref($q.dark.isActive);
    const changePasswordValue = {
      current: ref(''),
      new: ref(''),
      retype: ref(''),
    };
    const changePasswordLoadingState = ref(false);
    function changePasswordProcess() {
      changePasswordLoadingState.value = true;
      api
        .post('/autentikasi/changePassword', {
          ID_USER: user.value.ID_USER,
          PASSWORD_USER: changePasswordValue.new.value,
          PASSWORD_LAMA: changePasswordValue.current.value,
        })
        .then((response) => {
          $q.notify({
            message: 'Password change was successful!',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          changePasswordLoadingState.value = false;
          user.value = response.data;
          sessionStorage.setItem('user', JSON.stringify(response.data));
        })
        .catch((error) => {
          if (error.response.data.status == 401) {
            $q.notify({
              message: 'Current password is incorrect!',
              color: 'negative',
              icon: 'error',
              position: 'top-right',
            });
          } else {
            $q.notify({
              message: 'An error occurred while changing password!',
              color: 'negative',
              icon: 'error',
              position: 'top-right',
            });
          }
          changePasswordLoadingState.value = false;
        });
      changePasswordValue.current.value = '';
      changePasswordValue.new.value = '';
      changePasswordValue.retype.value = '';
    }
    function logout() {
      sessionStorage.removeItem('user');
      api.get('/autentikasi/logout');
      router.push('/');
    }
    return {
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
      user,
      miniState,
      settingsDialog,
      darkMode,
      switchToDarkMode() {
        $q.dark.toggle();
      },
      menus,
      changePasswordValue,
      changePasswordLoadingState,
      changePasswordProcess,
      logout,
      router,
    };
  },
};
</script>
