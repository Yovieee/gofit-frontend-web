<template>
  <div class="col text-h3 q-mx-md q-my-sm self-center">Weekly Schedules</div>
  <div v-if="data.length == 0" class="q-px-md fixed-center">
    <q-spinner size="xl" />
  </div>
  <div v-if="data.length != 0" class="q-px-md">
    <div>
      <q-tabs v-model="tab" align="left" narrow-indicator class="q-mb-md">
        <q-tab class="text-overline text-primary" name="morning" label="Morning" />
        <q-tab class="text-overline text-primary" name="evening" label="Evening" />
      </q-tabs>
      <div class="q-gutter-y-xs">
        <q-tab-panels v-model="tab" animated transition-prev="slide-right" transition-next="slide-left"
          class="bg-secondary text-white">
          <q-tab-panel name="morning">
            <div class="row">
              <div class="col-2 text-h6">#</div>
              <template v-for="morningTime in morningTimes" :key="morningTime">
                <div class="col text-h6 text-center">{{ morningTime.label }}</div>
              </template>
            </div>
          </q-tab-panel>
          <q-tab-panel name="evening">
            <div class="row">
              <div class="col-2 text-h6">#</div>
              <template v-for="eveningTime in eveningTimes" :key="eveningTime">
                <div class="col text-h6 text-center">{{ eveningTime.label }}</div>
              </template>
            </div>
          </q-tab-panel>
        </q-tab-panels>
        <template v-for="(dayName, index) in dayNames" :key="index">
          <q-tab-panels v-model="tab" animated transition-prev="slide-right" transition-next="slide-left"
            :class="$q.dark.isActive ? (index % 2 == 0 ? 'bg-grey-9' : 'bg-dark') : (index % 2 == 0 ? 'bg-grey-3' : 'bg-white')">
            <q-tab-panel name="morning">
              <div class="row">
                <div class="col-2 text-h6">{{ dayName.label }}</div>
                <div v-for="i in 9" :key="i" class="col text-center text-caption">
                  <q-list separator>
                    <q-item class="q-pt-sm" dense v-for="(morningSchedule, j) in data[index][i - 1]" :key="j">
                      <q-item-section>
                        <q-item-label>
                          {{ data[index][i - 1][j].NAMA_KELAS }}
                          <q-tooltip anchor="center left" self="center end">
                            Class name
                          </q-tooltip>
                        </q-item-label>
                        <q-item-label caption>
                          {{ data[index][i - 1][j].NAMA_USER }}
                          <q-tooltip anchor="center left" self="center end">
                            Instructor name
                          </q-tooltip>
                        </q-item-label>
                        <div class="q-gutter-x-xs text-right">
                          <q-btn flat color="positive" round size="xs" icon="edit"
                            @click="showEditDialog(data[index][i - 1][j])" />
                          <q-btn flat color="negative" round size="xs" icon="delete"
                            @click="deleteDataConfirm(data[index][i - 1][j])" />
                        </div>
                      </q-item-section>
                    </q-item>
                    <q-item dense clickable class="text-center" @click="showNewDialog(i - 1, index)">
                      <q-item-section>
                        <div>
                          <q-icon color="secondary" name="add" />
                          New
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
                    <q-item class="q-pt-sm" dense v-for="(eveningSchedule, j) in data[index][i + 8]" :key="j">
                      <q-item-section>
                        <q-item-label>
                          {{ data[index][i + 8][j].NAMA_KELAS }}
                          <q-tooltip anchor="center left" self="center end">
                            Class name
                          </q-tooltip>
                        </q-item-label>
                        <q-item-label caption>
                          {{ data[index][i + 8][j].NAMA_USER }}
                          <q-tooltip anchor="center left" self="center end">
                            Instructor name
                          </q-tooltip>
                        </q-item-label>
                        <div class="q-gutter-x-xs text-right">
                          <q-btn flat color="positive" round size="xs" icon="edit"
                            @click="showEditDialog(data[index][i + 8][j])" />
                          <q-btn flat color="negative" round size="xs" icon="delete"
                            @click="deleteDataConfirm(data[index][i + 8][j])" />
                        </div>
                      </q-item-section>
                    </q-item>
                    <q-item dense clickable class="text-center" @click="showNewDialog(i + 8, index)">
                      <q-item-section>
                        <div>
                          <q-icon color="secondary" name="add" />
                          New
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
  <q-dialog v-model="editDialog" persistent>
    <q-card style="min-width: 15cm;">
      <q-form
        @submit="$q.dialog({ title: 'Confirm', message: 'Are you sure you want to save this data?', cancel: true, persistent: true }).onOk(() => { saveEditData() })">
        <q-card-section class="q-pb-none row">
          <div class="text-h6 q-pt-xs col-auto">Edit Schedule for&nbsp;</div>
          <div class="col-auto">
            <q-select @update:model-value="val => refreshUnavailableInstructors(editData.scheduleSession.value, val)"
              class="text-h6" hide-dropdown-icon dense map-options v-model="editData.dayOfWeeklySchedule.value"
              :options="dayNames" borderless emit-value>
              <q-tooltip anchor="top middle" self="center middle">
                Click to change day of the week
              </q-tooltip>
            </q-select>
          </div>
          <div class="text-h6 q-pt-xs col-auto">&nbsp;at&nbsp;</div>
          <div class="col-auto">
            <q-select @update:model-value="val => refreshUnavailableInstructors(val, editData.dayOfWeeklySchedule.value)"
              class="text-h6" hide-dropdown-icon dense map-options v-model="editData.scheduleSession.value"
              :options="morningTimes.concat(eveningTimes)" borderless emit-value>
              <q-tooltip anchor="top middle" self="center middle">
                Click to change session
              </q-tooltip>
            </q-select>
          </div>
        </q-card-section>
        <q-card-section class="q-pb-none q-pt-lg">
          <q-field class="q-mb-md q-pb-xs" v-if="!(instructors.length > 0 && unavailableInstructors)" dense outlined
            label="Instructor" stack-label>
            <template v-slot:control>
              <div class="self-center full-width no-outline" tabindex="0"><q-skeleton /></div>
            </template>
          </q-field>
          <q-select v-if="instructors.length > 0 && unavailableInstructors" option-label="NAMA_USER"
            :options="instructors" autocomplete="NAMA_USER" dense outlined v-model="editData.instructor.value"
            label="Instructors"
            :rules="[val => !!val || 'Instructor is missing', val => ((editDataPrevious.instructor.value.ID_INSTRUKTUR == editData.instructor.value.ID_INSTRUKTUR && editDataPrevious.dayOfWeeklySchedule.value == editData.dayOfWeeklySchedule.value
                && editDataPrevious.scheduleSession.value == editData.scheduleSession.value) || unavailableInstructors.filter(ui => ui.ID_INSTRUKTUR == val.ID_INSTRUKTUR && ui.ID_INSTRUKTUR).length == 0) || 'This instructor is unavailable']">
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                  <q-avatar>
                    <q-icon v-if="scope.opt.FOTO_USER == null || scope.opt.FOTO_USER.length == 0" name="account_circle"
                      size="xl" color="grey" />
                    <img v-if="!(scope.opt.FOTO_USER == null || scope.opt.FOTO_USER.length == 0)"
                      :src="'data:image/jpeg;base64,' + scope.opt.FOTO_USER" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ scope.opt.NAMA_USER }}</q-item-label>
                  <q-item-label caption>ID: {{ scope.opt.ID_INSTRUKTUR }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </q-card-section>
        <q-card-section class="q-pb-none q-pt-xs">
          <q-field class="q-mb-md q-pb-xs" v-if="classes.length == 0" dense outlined label="Class" stack-label>
            <template v-slot:control>
              <div class="self-center full-width no-outline" tabindex="0"><q-skeleton /></div>
            </template>
          </q-field>
          <q-select v-if="classes.length > 0" option-label="NAMA_KELAS" :options="classes" dense outlined
            v-model="editData.class.value" label="Class" :rules="[val => !!val || 'Class is missing']" />
        </q-card-section>
        <q-card-section>
          <div class="row justify-end">
            <q-btn class="q-mr-sm" label="Cancel" color="primary" flat @click="closeEditDialog()" />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-dialog v-model="newDialog" persistent>
    <q-card style="min-width: 15cm;">
      <q-form
        @submit="$q.dialog({ title: 'Confirm', message: 'Are you sure you want to save this data?', cancel: true, persistent: true }).onOk(() => { saveNewData() })">
        <q-card-section class="q-pb-none row">
          <div class="text-h6 q-pt-xs col-auto">New Schedule for&nbsp;</div>
          <div class="col-auto">
            <q-select @update:model-value="val => refreshUnavailableInstructors(newData.scheduleSession.value, val)"
              class="text-h6" hide-dropdown-icon dense map-options v-model="newData.dayOfWeeklySchedule.value"
              :options="dayNames" borderless emit-value>
              <q-tooltip anchor="top middle" self="center middle">
                Click to change day of the week
              </q-tooltip>
            </q-select>
          </div>
          <div class="text-h6 q-pt-xs col-auto">&nbsp;at&nbsp;</div>
          <div class="col-auto">
            <q-select @update:model-value="val => refreshUnavailableInstructors(val, newData.dayOfWeeklySchedule.value)"
              class="text-h6" hide-dropdown-icon dense map-options v-model="newData.scheduleSession.value"
              :options="morningTimes.concat(eveningTimes)" borderless emit-value>
              <q-tooltip anchor="top middle" self="center middle">
                Click to change session
              </q-tooltip>
            </q-select>
          </div>
        </q-card-section>
        <q-card-section class="q-pb-none q-pt-lg">
          <q-field class="q-mb-md q-pb-xs" v-if="!(instructors.length > 0 && unavailableInstructors)" dense outlined
            label="Instructor" stack-label>
            <template v-slot:control>
              <div class="self-center full-width no-outline" tabindex="0"><q-skeleton /></div>
            </template>
          </q-field>
          <q-select v-if="instructors.length > 0 && unavailableInstructors" option-label="NAMA_USER"
            :options="instructors" dense outlined v-model="newData.instructor.value" label="Instructors"
            :rules="[val => !!val || 'Instructor is missing', val => unavailableInstructors.filter(ui => ui.ID_INSTRUKTUR == val.ID_INSTRUKTUR).length == 0 || 'This instructor is unavailable']">
            <template v-slot:option="scope">
              <q-item v-bind="scope.itemProps">
                <q-item-section avatar>
                  <q-avatar>
                    <q-icon v-if="scope.opt.FOTO_USER == null || scope.opt.FOTO_USER.length == 0" name="account_circle"
                      size="xl" color="grey" />
                    <img v-if="!(scope.opt.FOTO_USER == null || scope.opt.FOTO_USER.length == 0)"
                      :src="'data:image/jpeg;base64,' + scope.opt.FOTO_USER" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ scope.opt.NAMA_USER }}</q-item-label>
                  <q-item-label caption>ID: {{ scope.opt.ID_INSTRUKTUR }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </q-card-section>
        <q-card-section class="q-pb-none q-pt-xs">
          <q-field class="q-mb-md q-pb-xs" v-if="classes.length == 0" dense outlined label="Class" stack-label>
            <template v-slot:control>
              <div class="self-center full-width no-outline" tabindex="0"><q-skeleton /></div>
            </template>
          </q-field>
          <q-select v-if="classes.length > 0" option-label="NAMA_KELAS" :options="classes" autocomplete="NAMA_KELAS" dense
            outlined v-model="newData.class.value" label="Class" :rules="[val => !!val || 'Class is missing']" />
        </q-card-section>
        <q-card-section>
          <div class="row justify-end">
            <q-btn class="q-mr-sm" label="Cancel" color="primary" flat @click="closeNewDialog()" />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchWeeklySchedules">
        <q-item-section>Refresh</q-item-section>
      </q-item>
    </q-list>
  </q-menu>
</template>

<script>
import { nanoid } from 'nanoid'
import { defineComponent, onMounted, ref } from 'vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'
export default defineComponent({
  name: 'WeeklySchedulesComponent',
  setup() {
    const $q = useQuasar()
    const tab = ref('morning')
    const dayNames = [
      {
        label: 'Sunday',
        value: '0'
      },
      {
        label: 'Monday',
        value: '1'
      },
      {
        label: 'Tuesday',
        value: '2'
      },
      {
        label: 'Wednesday',
        value: '3'
      },
      {
        label: 'Thursday',
        value: '4'
      },
      {
        label: 'Friday',
        value: '5'
      },
      {
        label: 'Saturday',
        value: '6'
      }]
    const morningTimes = [
      {
        label: '06.00',
        value: '0'
      },
      {
        label: '06.30',
        value: '1'
      },
      {
        label: '07.00',
        value: '2'
      },
      {
        label: '07.30',
        value: '3'
      },
      {
        label: '08.00',
        value: '4'
      },
      {
        label: '08.30',
        value: '5'
      },
      {
        label: '09.00',
        value: '6'
      },
      {
        label: '09.30',
        value: '7'
      },
      {
        label: '10.00',
        value: '8'
      }]
    const eveningTimes = [
      {
        label: '17.00',
        value: '9'
      },
      {
        label: '17.30',
        value: '10'
      },
      {
        label: '18.00',
        value: '11'
      },
      {
        label: '18.30',
        value: '12'
      },
      {
        label: '19.00',
        value: '13'
      },
      {
        label: '19.30',
        value: '14'
      },
      {
        label: '20.00',
        value: '15'
      },
      {
        label: '20.30',
        value: '16'
      },
      {
        label: '21.00',
        value: '17'
      }]
    const data = ref([])
    const instructors = ref([])
    const unavailableInstructors = ref(null)
    const classes = ref([])
    const editDialog = ref(false)
    const editData = {
      idSchedule: ref(''),
      idWeeklySchedule: ref(''),
      instructor: ref(null),
      class: ref(null),
      scheduleSession: ref(null),
      dayOfWeeklySchedule: ref(null)
    }
    const editDataPrevious = {
      idSchedule: ref(''),
      idWeeklySchedule: ref(''),
      instructor: ref(null),
      class: ref(null),
      scheduleSession: ref(null),
      dayOfWeeklySchedule: ref(null)
    }
    const editDataPhotoHover = ref(false)
    const newDialog = ref(false)
    const newData = {
      idSchedule: ref(''),
      idWeeklySchedule: ref(''),
      instructor: ref(null),
      class: ref(null),
      scheduleSession: ref(''),
      dayOfWeeklySchedule: ref('')
    }
    const newDataPhotoHover = ref(false)
    const searchString = ref('')
    function fetchWeeklySchedules() {
      data.value = []
      api.get('/jadwalPaket', {
        withCredentials: true
      })
        .then(response => {
          data.value = response.data
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while fetching schedules',
            color: 'negative',
            position: 'top-right'
          })
        })
    }
    function refreshUnavailableInstructors(session, day) {
      unavailableInstructors.value = null
      api.post('/jadwalPaket/unavailableInstructors', {
        HARI_JADWAL_PAKET: day,
        SESI_JADWAL: session
      },
        {
          withCredentials: true
        })
        .then(response => {
          unavailableInstructors.value = response.data
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while getting unavailable instructors',
            color: 'negative',
            position: 'top-right'
          })
          newDialog.value = false
        })
    }
    function showEditDialog(schedule) {
      editDialog.value = true
      editData.idSchedule.value = schedule.ID_JADWAL
      editData.idWeeklySchedule.value = schedule.ID_JADWAL_PAKET
      editData.scheduleSession.value = schedule.SESI_JADWAL
      editData.dayOfWeeklySchedule.value = schedule.HARI_JADWAL_PAKET
      editData.instructor.value = instructors.value.filter(i => i.ID_INSTRUKTUR == schedule.ID_INSTRUKTUR)[0]
      editData.class.value = classes.value.filter(c => c.ID_KELAS == schedule.ID_KELAS)[0]
      editDataPrevious.idSchedule.value = schedule.ID_JADWAL
      editDataPrevious.idWeeklySchedule.value = schedule.ID_JADWAL_PAKET
      editDataPrevious.scheduleSession.value = schedule.SESI_JADWAL
      editDataPrevious.dayOfWeeklySchedule.value = schedule.HARI_JADWAL_PAKET
      editDataPrevious.instructor.value = instructors.value.filter(i => i.ID_INSTRUKTUR == schedule.ID_INSTRUKTUR)[0]
      editDataPrevious.class.value = classes.value.filter(c => c.ID_KELAS == schedule.ID_KELAS)[0]
      refreshUnavailableInstructors(schedule.SESI_JADWAL, schedule.HARI_JADWAL_PAKET)
    }
    function closeEditDialog() {
      editDialog.value = false
      editData.idSchedule.value = ''
      editData.idWeeklySchedule.value = ''
      editData.instructor.value = null
      editData.class.value = null
      editData.scheduleSession.value = null
      editData.dayOfWeeklySchedule.value = null
      editDataPrevious.idSchedule.value = ''
      editDataPrevious.idWeeklySchedule.value = ''
      editDataPrevious.instructor.value = null
      editDataPrevious.class.value = null
      editDataPrevious.scheduleSession.value = null
      editDataPrevious.dayOfWeeklySchedule.value = null
    }

    function showNewDialog(session, day) {
      newDialog.value = true
      newData.idSchedule.value = nanoid(32)
      newData.idWeeklySchedule.value = nanoid(32)
      newData.scheduleSession.value = session.toString()
      newData.dayOfWeeklySchedule.value = day.toString()
      refreshUnavailableInstructors(session, day)
    }
    function closeNewDialog() {
      newDialog.value = false
      newData.idSchedule.value = ''
      newData.idWeeklySchedule.value = ''
      newData.instructor.value = null
      newData.class.value = null
      newData.scheduleSession.value = null
      newData.dayOfWeeklySchedule.value = null
    }
    function deleteData(idSchedule, idWeeklySchedule) {
      api.post('/jadwalPaket/delete', {
        ID_JADWAL: idSchedule,
        ID_JADWAL_PAKET: idWeeklySchedule
      }, { withCredentials: true })
        .then(() => {
          $q.notify({
            message: 'Success deleting schedule',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right'
          })
          fetchWeeklySchedules()
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while deleting schedule',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right'
          })
        })
    }
    function deleteDataConfirm(schedule) {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to delete this schedule? This action cannot be undone.',
        cancel: true,
        persistent: true,
        ok: {
          color: 'negative',
          label: 'Delete'
        }
      }).onOk(() => {
        deleteData(schedule.ID_JADWAL, schedule.ID_JADWAL_PAKET)
      })
    }
    function saveEditData() {
      api.post('/jadwalPaket/update', {
        ID_JADWAL: editData.idSchedule.value,
        ID_KELAS: editData.class.value.ID_KELAS,
        ID_USER: editData.instructor.value.ID_USER,
        ID_INSTRUKTUR: editData.instructor.value.ID_INSTRUKTUR,
        SESI_JADWAL: editData.scheduleSession.value,
        HARI_JADWAL_PAKET: editData.dayOfWeeklySchedule.value,
        ID_JADWAL_PAKET: editData.idWeeklySchedule.value
      }, { withCredentials: true })
        .then(() => {
          $q.notify({
            message: 'Success editing schedule',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right'
          })
          fetchWeeklySchedules()
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while editing schedule',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right'
          })
        })
      closeEditDialog()
    }
    function saveNewData() {
      api.post('/jadwalPaket/create', {
        ID_JADWAL: newData.idSchedule.value,
        ID_KELAS: newData.class.value.ID_KELAS,
        ID_USER: newData.instructor.value.ID_USER,
        ID_INSTRUKTUR: newData.instructor.value.ID_INSTRUKTUR,
        SESI_JADWAL: newData.scheduleSession.value,
        HARI_JADWAL_PAKET: newData.dayOfWeeklySchedule.value,
        ID_JADWAL_PAKET: newData.idWeeklySchedule.value
      }, { withCredentials: true })
        .then(() => {
          $q.notify({
            message: 'Success adding schedule',
            color: 'positive',
            icon: 'check_circle',
            position: 'top-right'
          })
          fetchWeeklySchedules()
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while adding schedule',
            color: 'negative',
            icon: 'cancel',
            position: 'top-right'
          })
        })
      closeNewDialog()
    }
    onMounted(() => {
      fetchWeeklySchedules()
      api.get('/instruktur/',
        {
          withCredentials: true
        })
        .then(response => {
          instructors.value = response.data
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while getting list of instructors',
            color: 'negative',
            position: 'top-right'
          })
        })
      api.get('/kelas/',
        {
          withCredentials: true
        })
        .then(response => {
          classes.value = response.data
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while getting list of classes',
            color: 'negative',
            position: 'top-right'
          })
        })
    })
    return {
      $q,
      dayNames,
      morningTimes,
      eveningTimes,
      data,
      instructors,
      unavailableInstructors,
      classes,
      editData,
      editDataPrevious,
      editDialog,
      editDataPhotoHover,
      showEditDialog,
      closeEditDialog,
      deleteDataConfirm,
      saveEditData,
      newData,
      newDialog,
      newDataPhotoHover,
      showNewDialog,
      closeNewDialog,
      saveNewData,
      searchString,
      fetchWeeklySchedules,
      refreshUnavailableInstructors,
      tab,
      console
    }
  }
});
</script>
