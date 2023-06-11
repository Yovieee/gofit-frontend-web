<template>
  <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">
    Instructor Attendances
  </div>
  <div class="q-px-md">
    <div v-if="rows.length == 0" class="q-px-md fixed-center">
      <q-spinner size="xl" />
    </div>
    <q-table
      :filter="searchString"
      :rows="rows"
      :columns="columns"
      row-key="id"
      :rows-per-page-options="[6]"
      v-if="rows.length != 0"
    >
      <template v-slot:top>
        <div class="col">
          <div class="row q-mb-xs">
            <q-input
              outlined
              dense
              v-model="searchString"
              label="Search here..."
            >
              <template v-slot:append>
                <q-icon
                  v-if="searchString !== ''"
                  name="close"
                  @click="searchString = ''"
                  class="cursor-pointer"
                />
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="row q-gutter-md">
            <q-btn
              size="xs"
              dense
              flat
              label="All"
              @click="searchString = ''"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Unassigned"
              @click="searchString = 'Unassigned'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Absent"
              @click="searchString = 'Absent'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Attended"
              @click="searchString = 'Attended'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Late"
              @click="searchString = 'Late'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Not Yet Approved Permission"
              @click="searchString = 'Not Yet Approved Permission'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Permission Approved"
              @click="searchString = 'Permission Approved'"
            />
            <q-btn
              size="xs"
              dense
              flat
              label="Permission Refused"
              @click="searchString = 'Permission Refused'"
            />
          </div>
        </div>
      </template>
      <template v-slot:body-cell-action="props">
        <q-td :props="props">
          <template v-if="props.row.STATUS_PRESENSI_INSTRUKTUR == 3">
            <q-btn
              class="q-mx-xs"
              color="negative"
              label="Refuse"
              dense
              @click="
                $q.dialog({
                  title: 'Confirm',
                  message: 'Are you sure you want to refuse this permission?',
                  cancel: true,
                  persistent: true,
                }).onOk(() => {
                  api
                    .post(
                      'presensiInstruktur/tolakIzin',
                      {
                        ID_PRESENSI_INSTRUKTUR:
                          props.row.ID_PRESENSI_INSTRUKTUR,
                        PEG_ID_USER: JSON.parse(sessionStorage.getItem('user'))
                          .ID_USER,
                        ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user'))
                          .ID_PEGAWAI,
                      },
                      { withCredentials: true }
                    )
                    .then(() => {
                      fetchInstructorAttendances();
                      $q.notify({
                        message: 'Permission Refused',
                        color: 'positive',
                        position: 'top-right',
                      });
                    })
                    .catch(() => {
                      $q.notify({
                        message: 'An error occurred while refusing permission',
                        color: 'negative',
                        position: 'top-right',
                      });
                    });
                })
              "
            />
            <q-btn
              class="q-mx-xs"
              color="positive"
              label="Approve"
              dense
              @click="
                openApprovePermissionDialog(
                  props.row.ID_PRESENSI_INSTRUKTUR,
                  props.row.JADWAL.SESI_JADWAL,
                  props.row.JADWAL.TANGGAL_JADWAL_HARIAN,
                  props.row.JADWAL.ID_JADWAL
                )
              "
            />
          </template>
        </q-td>
      </template>
    </q-table>
  </div>
  <q-dialog v-model="approvePermissionDialog" persistent>
    <q-card style="min-width: 15cm">
      <q-form
        @submit="
          $q.dialog({
            title: 'Confirm',
            message: 'Are you sure you want to select this decision?',
            cancel: true,
            persistent: true,
          }).onOk(() => {
            approvePermissionDialog = false;
            api
              .post(
                'presensiInstruktur/terimaIzin',
                {
                  ID_JADWAL: approvePermissionData.scheduleId.value,
                  ID_PRESENSI_INSTRUKTUR: approvePermissionData.id.value,
                  ID_INSTRUKTUR:
                    approvePermissionData.decision.value == 1
                      ? approvePermissionData.instructor.value.ID_INSTRUKTUR
                      : null,
                  PEG_ID_USER: JSON.parse(sessionStorage.getItem('user'))
                    .ID_USER,
                  ID_PEGAWAI: JSON.parse(sessionStorage.getItem('user'))
                    .ID_PEGAWAI,
                },
                { withCredentials: true }
              )
              .then(() => {
                fetchInstructorAttendances();
                $q.notify({
                  message: 'Permission Approved',
                  color: 'positive',
                  position: 'top-right',
                });
              })
              .catch(() => {
                $q.notify({
                  message: 'An error occurred while approving permission',
                  color: 'negative',
                  position: 'top-right',
                });
              });
          })
        "
      >
        <q-card-section class="q-pb-none">
          <div class="text-h6 q-pt-xs">Approve Permission</div>
        </q-card-section>
        <q-card-section class="q-pb-none">
          <div class="text-caption">Select the decision you want to make</div>
        </q-card-section>
        <q-card-section class="q-pb-none q-pt-lg row">
          <div class="col-auto">
            <q-option-group
              :options="[
                { label: 'Cancel the schedule', value: 0 },
                { label: 'Replace the instructor with', value: 1 },
              ]"
              v-model="approvePermissionData.decision.value"
            />
          </div>
          <div class="col-6 q-pl-md">
            <br />
            <br />
            <q-field
              class="q-mb-md q-pb-xs"
              v-if="!(instructors.length > 0 && unavailableInstructors)"
              dense
              outlined
              label="Instructor"
              stack-label
              :disable="approvePermissionData.decision.value != 1"
            >
              <template v-slot:control>
                <div class="self-center full-width no-outline" tabindex="0">
                  <q-skeleton />
                </div>
              </template>
            </q-field>
            <q-select
              v-if="instructors.length > 0 && unavailableInstructors"
              option-label="NAMA_USER"
              :options="instructors"
              dense
              outlined
              :disable="approvePermissionData.decision.value != 1"
              v-model="approvePermissionData.instructor.value"
              label="Instructors"
              :rules="[
                (val) => !!val || 'Instructor is missing',
                (val) =>
                  unavailableInstructors.filter(
                    (ui) => ui.ID_INSTRUKTUR == val.ID_INSTRUKTUR
                  ).length == 0 || 'This instructor is unavailable',
              ]"
            >
              <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section avatar>
                    <q-avatar>
                      <q-icon
                        v-if="
                          scope.opt.FOTO_USER == null ||
                          scope.opt.FOTO_USER.length == 0
                        "
                        name="account_circle"
                        size="xl"
                        color="grey"
                      />
                      <img
                        v-if="
                          !(
                            scope.opt.FOTO_USER == null ||
                            scope.opt.FOTO_USER.length == 0
                          )
                        "
                        :src="'data:image/jpeg;base64,' + scope.opt.FOTO_USER"
                      />
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ scope.opt.NAMA_USER }}</q-item-label>
                    <q-item-label caption
                      >ID: {{ scope.opt.ID_INSTRUKTUR }}</q-item-label
                    >
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row justify-end">
            <q-btn
              class="q-mr-sm"
              label="Cancel"
              color="primary"
              flat
              @click="closeApprovePermissionDialog()"
            />
            <q-btn type="submit" label="Save" color="primary" />
          </div>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
  <q-menu touch-position context-menu>
    <q-list dense>
      <q-item clickable v-close-popup @click="fetchInstructorAttendances">
        <q-item-section>Refresh</q-item-section>
      </q-item>
    </q-list>
  </q-menu>
</template>

<script>
import { nanoid } from 'nanoid';
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
export default defineComponent({
  name: 'InstructorAttendancesComponent',
  setup() {
    const $q = useQuasar();
    const columns = [
      {
        name: 'employeeName',
        required: false,
        label: 'Employee Name',
        align: 'left',
        field: (row) => row.NAMA_USER,
        format: (val) => `${val == null ? 'Unassigned' : val}`,
        sortable: true,
      },
      {
        name: 'instructorName',
        required: false,
        label: 'Instructor Name',
        align: 'left',
        field: (row) => row.JADWAL.NAMA_USER,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'class',
        required: false,
        label: 'Class',
        align: 'left',
        field: (row) => row.JADWAL.NAMA_KELAS,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'date',
        required: false,
        label: 'Date',
        align: 'left',
        field: (row) => row.JADWAL.TANGGAL_JADWAL_HARIAN,
        format: (val) =>
          `${
            [
              'Sunday',
              'Monday',
              'Tuesday',
              'Wednesday',
              'Thursday',
              'Friday',
              'Saturday',
            ][new Date(val).getDay()] +
            ', ' +
            new Date(val).getDate() +
            ' ' +
            new Date(val).toLocaleString('default', { month: 'long' }) +
            ' ' +
            new Date(val).getFullYear()
          }`,
        sortable: true,
      },
      {
        name: 'session',
        required: false,
        label: 'Session',
        align: 'left',
        field: (row) => row.JADWAL.SESI_JADWAL,
        format: (val) =>
          `${
            [
              '06.00',
              '06.30',
              '07.00',
              '07.30',
              '08.00',
              '08.30',
              '09.00',
              '09.30',
              '10.00',
              '17.00',
              '17.30',
              '18.00',
              '18.30',
              '19.00',
              '19.30',
              '20.00',
              '20.30',
              '21.00',
            ][val]
          }`,
        sortable: true,
      },
      {
        name: 'status',
        required: false,
        label: 'Status',
        align: 'left',
        field: (row) => row.STATUS_PRESENSI_INSTRUKTUR,
        format: (val) =>
          `${
            val == null
              ? 'Unassigned'
              : val == 0
              ? 'Absent'
              : val == 1
              ? 'Attended'
              : val == 2
              ? 'Late'
              : val == 3
              ? 'Not Yet Approved Permission'
              : val == 4
              ? 'Permission Approved'
              : 'Permission Refused'
          }`,
        sortable: true,
      },
      {
        name: 'statement',
        required: false,
        label: 'Statement',
        align: 'left',
        field: (row) => row.KETERANGAN_PRESENSI_INSTRUKTUR,
        format: (val) => `${val == null ? '' : val}`,
        sortable: true,
      },
      {
        name: 'finishedOn',
        required: false,
        label: 'Finished on',
        align: 'left',
        field: (row) => row.JAM_SELESAI_PRESENSI_INSTRUKTUR,
        format: (val) => `${val == null ? 'Unassigned' : val}`,
        sortable: true,
      },
      {
        name: 'action',
        required: false,
        label: 'Action',
        align: 'left',
      },
    ];
    const rows = ref([]);
    const searchString = ref('');
    const instructors = ref([]);
    const unavailableInstructors = ref(null);
    const approvePermissionDialog = ref(false);
    const approvePermissionData = {
      id: ref(null),
      instructor: ref(null),
      decision: ref(0),
      scheduleId: ref(null),
    };
    function openApprovePermissionDialog(id, session, date, scheduleId) {
      refreshUnavailableInstructors(session, date);
      approvePermissionData.id.value = id;
      approvePermissionData.decision.value = 0;
      approvePermissionData.instructor.value = null;
      approvePermissionData.scheduleId.value = scheduleId;
      approvePermissionDialog.value = true;
    }
    function closeApprovePermissionDialog() {
      approvePermissionDialog.value = false;
    }
    async function fetchInstructorAttendances() {
      rows.value = [];
      return api
        .get('/presensiInstruktur', {
          withCredentials: true,
        })
        .then((response) => {
          rows.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while fetching users',
            color: 'negative',
            position: 'top-right',
          });
        });
    }
    function refreshUnavailableInstructors(session, date) {
      unavailableInstructors.value = null;
      api
        .post(
          '/presensiInstruktur/unavailableInstructors',
          {
            TANGGAL_JADWAL_HARIAN: date,
            SESI_JADWAL: session,
          },
          {
            withCredentials: true,
          }
        )
        .then((response) => {
          unavailableInstructors.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while getting unavailable instructors',
            color: 'negative',
            position: 'top-right',
          });
          newDialog.value = false;
        });
    }
    onMounted(() => {
      fetchInstructorAttendances();
      api
        .get('/instruktur/', {
          withCredentials: true,
        })
        .then((response) => {
          instructors.value = response.data;
        })
        .catch(() => {
          $q.notify({
            message: 'An error occurred while getting list of instructors',
            color: 'negative',
            position: 'top-right',
          });
        });
    });
    return {
      columns,
      rows,
      searchString,
      instructors,
      fetchInstructorAttendances,
      refreshUnavailableInstructors,
      unavailableInstructors,
      approvePermissionDialog,
      approvePermissionData,
      openApprovePermissionDialog,
      closeApprovePermissionDialog,
      nanoid,
      api,
      sessionStorage,
    };
  },
});
</script>
