<template>
  <div id="everything">
    <div class="col text-h3 q-mx-md q-mt-md q-mb-md self-center">
      Income Reports
    </div>
    <div class="q-px-md">
      <div class="row">
        <div class="col-auto q-mt-sm">Year:&nbsp;&nbsp;&nbsp;&nbsp;</div>
        <div class="col-auto">
          <q-input
            v-model="year"
            dense
            borderless
            @keydown.enter.prevent="fetchIncomeReports"
            @blur="fetchIncomeReports"
            :rules="[(val) => /^\d+$/.test(val) || 'Invalid year']"
          >
            <q-tooltip anchor="center right" self="center end">
              Click to change year
            </q-tooltip>
          </q-input>
        </div>
      </div>
      <div v-if="income.length > 0">
        Generated at:&nbsp;&nbsp;&nbsp;&nbsp;{{
          new Date().toISOString().substring(0, 10)
        }}
      </div>
      <div v-if="income.length == 0" class="q-px-md fixed-center">
        <q-spinner size="xl" />
      </div>
      <div
        id="tableAndCanvas"
        :style="income.length > 0 ? 'display: block' : 'display: none'"
      >
        <canvas id="myChart"></canvas>
        <q-table
          id="incomeTable"
          class="q-mt-xl"
          :rows="income"
          :columns="columns"
          :rows-per-page-options="[12]"
        >
          <template v-slot:bottom>
            <q-space />
            <strong
              >Sum of Income
              {{
                income
                  .map(
                    (item) =>
                      parseInt(item.AKTIVASI) + parseInt(item.DEPOSIT_UANG)
                  )
                  .reduce((a, b) => a + b, 0)
              }}</strong
            >
          </template>
        </q-table>
      </div>
    </div>
    <q-menu touch-position context-menu>
      <q-list dense>
        <q-item clickable v-close-popup @click="fetchIncomeReports">
          <q-item-section>Refresh</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="exportToPDF">
          <q-item-section>Export</q-item-section>
        </q-item>
      </q-list>
    </q-menu>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'boot/axios';
import { useQuasar } from 'quasar';
import jsPDF from 'jspdf';
import Chart from 'chart.js/auto';
import html2canvas from 'html2canvas';
export default defineComponent({
  name: 'IncomeReportsComponent',
  setup() {
    const $q = useQuasar();
    const income = ref([]);
    const year = ref(new Date().getFullYear());
    const columns = [
      {
        name: 'month',
        required: false,
        label: 'Month',
        align: 'left',
        field: (row) => row.BULAN,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'activation',
        required: false,
        label: 'Activation',
        align: 'left',
        field: (row) => row.AKTIVASI,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'deposit',
        required: false,
        label: 'Deposit',
        align: 'left',
        field: (row) => row.DEPOSIT_UANG,
        format: (val) => `${val}`,
        sortable: true,
      },
      {
        name: 'total',
        required: false,
        label: 'Total',
        align: 'left',
        field: (row) => row,
        format: (val) =>
          `${parseInt(val.AKTIVASI) + parseInt(val.DEPOSIT_UANG)}`,
        sortable: true,
      },
    ];
    async function fetchIncomeReports() {
      if (/^\d+$/.test(year.value)) {
        income.value = [];
        var ctx = document.getElementById('myChart');
        ctx.remove();
        ctx = document.createElement('canvas');
        ctx.id = 'myChart';
        document.getElementById('tableAndCanvas').prepend(ctx);
        return api
          .post(
            '/laporan/laporanPendapatan',
            {
              TAHUN: year.value,
            },
            {
              withCredentials: true,
            }
          )
          .then((response) => {
            $q.notify({
              message: 'Successfully generated income reports',
              color: 'positive',
              position: 'top-right',
            });
            income.value = response.data;
            ctx.height = 75;
            new Chart(ctx, {
              type: 'bar',
              data: {
                labels: [
                  'January',
                  'February',
                  'March',
                  'April',
                  'May',
                  'June',
                  'July',
                  'August',
                  'September',
                  'October',
                  'November',
                  'Desember',
                ],
                datasets: [
                  {
                    label: 'Total Income',
                    data: income.value.map(
                      (income) =>
                        parseInt(income.DEPOSIT_UANG) +
                        parseInt(income.AKTIVASI)
                    ),
                    borderWidth: 1,
                  },
                ],
              },
              options: {
                scales: {
                  y: {
                    beginAtZero: true,
                  },
                },
              },
            });
          })
          .catch(() => {
            $q.notify({
              message: 'An error occurred while generating income reports',
              color: 'negative',
              position: 'top-right',
            });
          });
      }
    }
    function exportToPDF() {
      const element = document.getElementById('everything');
      const doc = new jsPDF();
      html2canvas(element).then((canvas) => {
        const imageData = canvas.toDataURL('image/png', 1.0);
        const pdfWidth = doc.internal.pageSize.getWidth();
        const pdfHeight = doc.internal.pageSize.getHeight();
        const elementAspectRatio = canvas.width / canvas.height;
        let scaledWidth = pdfWidth;
        let scaledHeight = pdfWidth / elementAspectRatio;
        if (scaledHeight > pdfHeight) {
          scaledHeight = pdfHeight;
          scaledWidth = pdfHeight * elementAspectRatio;
        }
        doc.addImage(imageData, 'PNG', 0, 25, scaledWidth, scaledHeight);
        doc.save('IncomeReport_' + new Date().toISOString() + '.pdf');
      });
    }
    onMounted(() => {
      fetchIncomeReports();
    });
    return {
      income,
      columns,
      year,
      fetchIncomeReports,
      document,
      exportToPDF,
      Date,
    };
  },
});
</script>
