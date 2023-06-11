<template>
  <div class="col text-h3 q-mx-md q-my-sm self-center">Daily Schedules</div>
  <div v-if="data.length == 0" class="q-px-md fixed-center">
    <q-spinner size="xl" />
  </div>
  <div v-if="data.length != 0 && isGenerated" class="q-px-md">
    <div>
      <q-tabs v-model="tab" align="left" narrow-indicator class="q-mb-md">
        <q-tab
          class="text-overline text-primary"
          name="morning"
          label="Morning"
        />
        <q-tab
          class="text-overline text-primary"
          name="evening"
          label="Evening"
        />
      </q-tabs>
      <div class="q-gutter-y-xs">
        <q-tab-panels
          v-model="tab"
          animated
          transition-prev="slide-right"
          transition-next="slide-left"
          class="bg-secondary text-white"
        >
          <q-tab-panel name="morning">
            <div class="row">
              <div class="col-2 text-h6">#</div>
              <template v-for="morningTime in morningTimes" :key="morningTime">
                <div class="col text-h6 text-center">
                  {{ morningTime.label }}
                </div>
              </template>
            </div>
          </q-tab-panel>
          <q-tab-panel name="evening">
            <div class="row">
              <div class="col-2 text-h6">#</div>
              <template v-for="eveningTime in eveningTimes" :key="eveningTime">
                <div class="col text-h6 text-center">
                  {{ eveningTime.label }}
                </div>
              </template>
            </div>
          </q-tab-panel>
        </q-tab-panels>
        <template v-for="(dayName, index) in dayNames" :key="index">
          <q-tab-panels
            v-model="tab"
            animated
            transition-prev="slide-right"
            transition-next="slide-left"
            :class="
              $q.dark.isActive
                ? index % 2 == 0
                  ? 'bg-grey-9'
                  : 'bg-dark'
                : index % 2 == 0
                ? 'bg-grey-3'
                : 'bg-white'
            "
          >
            <q-tab-panel name="morning">
              <div class="row">
                <div class="col-2 text-h6">{{ dayName.label }}</div>
                <div
                  v-for="i in 9"
                  :key="i"
                  class="col text-center text-caption"
                >
                  <q-list separator>
                    <q-item
                      class="q-pt-sm"
                      dense
                      v-for="(morningSchedule, j) in data[index][i - 1]"
                      :key="j"
                    >
                      <q-item-section>
                        <q-item-label>
                          {{ data[index][i - 1][j].NAMA_KELAS }}
                          <q-tooltip anchor="center left" self="center end">
                            Class name
                          </q-tooltip>
                        </q-item-label>
                        <q-item-label caption>
                          {{ data[index][i - 1][j].NAMA_USER }}
                          <template
                            v-if="data[index][i - 1][j].JAD_ID_JADWAL != null"
                          >
                            <br />
                            {{
                              '(replacing ' +
                              data[index][i - 1][j].NAMA_INSTRUKTUR_SEBELUMNYA +
                              ')'
                            }}</template
                          >
                          <q-tooltip anchor="center left" self="center end">
                            Instructor name
                          </q-tooltip>
                        </q-item-label>
                        <q-item-label
                          v-if="
                            data[index][i - 1][j].IS_LIBUR_JADWAL_HARIAN == 1
                          "
                        >
                          <q-chip
                            class="text-white"
                            size="xs"
                            color="primary"
                            label="CANCELED"
                          />
                        </q-item-label>
                        <div
                          v-if="
                            data[index][i - 1][j].IS_LIBUR_JADWAL_HARIAN != 1
                          "
                          class="q-gutter-x-xs text-right"
                        >
                          <q-btn
                            flat
                            color="negative"
                            round
                            size="xs"
                            icon="event_busy"
                            @click="
                              cancelScheduleConfirm(data[index][i - 1][j])
                            "
                          />
                        </div>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </q-tab-panel>
            <q-tab-panel name="evening">
              <div class="row">
                <div class="col-2 text-h6">{{ dayName.label }}</div>
                <div v-for="i in 9" :key="i" class="col text-center">
                  <q-list separator>
                    <q-item
                      class="q-pt-sm"
                      dense
                      v-for="(eveningSchedule, j) in data[index][i + 8]"
                      :key="j"
                    >
                      <q-item-section>
                        <q-item-label>
                          {{ data[index][i + 8][j].NAMA_KELAS }}
                          <q-tooltip anchor="center left" self="center end">
                            Class name
                          </q-tooltip>
                        </q-item-label>
                        <q-item-label caption>
                          {{ data[index][i + 8][j].NAMA_USER }}
                          <template
                            v-if="data[index][i + 8][j].JAD_ID_JADWAL != null"
                          >
                            <br />
                            {{
                              '(replacing ' +
                              data[index][i + 8][j].NAMA_INSTRUKTUR_SEBELUMNYA +
                              ')'
                            }}</template
                          >
                          <q-tooltip anchor="center left" self="center end">
                            Instructor name
                          </q-tooltip>
                        </q-item-label>
                        <q-item-label
                          v-if="
                            data[index][i + 8][j].IS_LIBUR_JADWAL_HARIAN == 1
                          "
                        >
                          <q-chip
                            class="text-white"
                            size="xs"
                            color="primary"
                            label="CANCELED"
                          />
                        </q-item-label>
                        <div
                          v-if="
                            data[index][i + 8][j].IS_LIBUR_JADWAL_HARIAN != 1
                          "
                          class="q-gutter-x-xs text-right"
                        >
                          <q-btn
                            flat
                            color="negative"
                            round
                            size="xs"
                            icon="event_busy"
                            @click="
                              cancelScheduleConfirm(data[index][i + 8][j])
                            "
                          />
                        </div>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </q-tab-panel>
          </q-tab-panels>
        </template>
      </div>
    </div>
  </div>
  <div
    v-if="data.length != 0 && !isGenerated"
    class="q-px-md fixed-center text-center"
  >
    <div class="text-overline">Not generated yet</div>
    <q-btn
      class="q-mt-md"
      color="primary"
      label="Generate Now"
      @click="
        $q.dialog({
          title: 'Confirm',
          message: 'Are you sure you want to generate schedule for this week?',
          cancel: true,
          persistent: true,
        }).onOk(() => {
          api
            .get('jadwalHarian/generate', { withCredentials: true })
            .then(() => {
              $q.notify({
                message: 'Success generating schedule',
                color: 'positive',
                icon: 'check_circle',
                position: 'top-right',
              });
              fetchDailySchedules();
            })
            .catch(() => {
              $q.notify({
                message: 'An error occurred while generating schedule',
                color: 'negative',
                icon: 'cancel',
                position: 'top-right',
              });
            });
        })
      "
    />
  </div>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchDailySchedules">
        <q-item-section>Refresh</q-item-section>
      </q-item>
    </q-list>
  </q-menu>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
export default defineComponent({
  name: 'DailySchedulesComponent',
  setup() {
    const $q = useQuasar();
    const tab = ref('morning');
    const isGenerated = ref(false);
    const lastSunday = ref(new Date());
    lastSunday.value.setDate(
      lastSunday.value.getDate() - lastSunday.value.getDay()
    );
    const lastMonday = ref(new Date());
    lastMonday.value.setDate(
      lastMonday.value.getDate() - lastMonday.value.getDay() + 1
    );
    const lastTuesday = ref(new Date());
    lastTuesday.value.setDate(
      lastTuesday.value.getDate() - lastTuesday.value.getDay() + 2
    );
    const lastWednesday = ref(new Date());
    lastWednesday.value.setDate(
      lastWednesday.value.getDate() - lastWednesday.value.getDay() + 3
    );
    const lastThursday = ref(new Date());
    lastThursday.value.setDate(
      lastThursday.value.getDate() - lastThursday.value.getDay() + 4
    );
    const lastFriday = ref(new Date());
    lastFriday.value.setDate(
      lastFriday.value.getDate() - lastFriday.value.getDay() + 5
    );
    const lastSaturday = ref(new Date());
    lastSaturday.value.setDate(
      lastSaturday.value.getDate() - lastSaturday.value.getDay() + 6
    );
    const dayNames = [
      {
        label:
          'Sunday, ' +
          lastSunday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '0',
      },
      {
        label:
          'Monday, ' +
          lastMonday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '1',
      },
      {
        label:
          'Tuesday, ' +
          lastTuesday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '2',
      },
      {
        label:
          'Wednesday, ' +
          lastWednesday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '3',
      },
      {
        label:
          'Thursday, ' +
          lastThursday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '4',
      },
      {
        label:
          'Friday, ' +
          lastFriday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '5',
      },
      {
        label:
          'Saturday, ' +
          lastSaturday.value.toLocaleString('en-UK', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
          }),
        value: '6',
      },
    ];
    const morningTimes = [
      {
        label: '06.00',
        value: '0',
      },
      {
        label: '06.30',
        value: '1',
      },
      {
        label: '07.00',
        value: '2',
      },
      {
        label: '07.30',
        value: '3',
      },
      {
        label: '08.00',
        value: '4',
      },
      {
        label: '08.30',
        value: '5',
      },
      {
        label: '09.00',
        value: '6',
      },
      {
        label: '09.30',
        value: '7',
      },
      {
        label: '10.00',
        value: '8',
      },
    ];
    const eveningTimes = [
      {
        label: '17.00',
        value: '9',
      },
      {
        label: '17.30',
        value: '10',
      },
      {
        label: '18.00',
        value: '11',
      },
      {
        label: '18.30',
        value: '12',
      },
      {
        label: '19.00',
        value: '13',
      },
      {
        label: '19.30',
        value: '14',
      },
      {
        label: '20.00',
        value: '15',
      },
      {
        label: '20.30',
        value: '16',
      },
      {
        label: '21.00',
        value: '17',
      },
    ];
    const data = ref([]);
    const searchString = ref('');
    function fetchDailySchedules() {
      data.value = [];
      isGenerated.value = false;
      api
        .get('/jadwalHarian', {
          withCredentials: true,
        })
        .then((response) => {
          for (let i = 0; i < 7; i++) {
            for (let j = 0; j < 18; j++) {
              if (response.data[i][j].length > 0) {
                isGenerated.value = true;
              }
            }
          }
          data.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while fetching schedules',
            color: 'negative',
            position: 'top-right',
          });
        });
    }
    function cancelSchedule(idSchedule, idDailySchedule) {
      api
        .post(
          '/jadwalHarian/jadikanLibur',
          {
            ID_JADWAL: idSchedule,
            ID_JADWAL_HARIAN: idDailySchedule,
            PEG_ID_USER: JSON.parse(sessionStorage.getItem('user')).ID_USER,
            ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user')).ID_PEGAWAI,
          },
          { withCredentials: true }
        )
        .then(() => {
          $q.notify({
            message: 'Success canceling the schedule',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right',
          });
          fetchDailySchedules();
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while canceling the schedule',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right',
          });
        });
    }
    function cancelScheduleConfirm(schedule) {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to cancel this schedule?',
        cancel: true,
        persistent: true,
        ok: {
          color: 'negative',
          label: 'Yes',
        },
      }).onOk(() => {
        cancelSchedule(schedule.ID_JADWAL, schedule.ID_JADWAL_HARIAN);
      });
    }
    onMounted(() => {
      fetchDailySchedules();
    });
    return {
      $q,
      dayNames,
      morningTimes,
      eveningTimes,
      data,
      cancelScheduleConfirm,
      searchString,
      fetchDailySchedules,
      tab,
      isGenerated,
      api,
    };
  },
});
</script>
