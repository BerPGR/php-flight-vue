<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-toolbar-title>Dashboard (Quasar + Vue + Flight)</q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="q-pa-lg">
        <q-form @submit.prevent="add" class="row q-col-gutter-md q-mb-lg">
          <div class="col-12 col-md-5">
            <q-input v-model="name" label="Nome do projeto" dense outlined />
          </div>
          <div class="col-12 col-md-3">
            <q-select v-model="status" :options="statusOption" label="Status" dense outlined emit-value map-options />
          </div>
          <div class="col-12 col-md-2 flex items-end">
            <q-btn type="submit" label="Criar" color="red" class="full-width" />
          </div>
        </q-form>

        <q-table :rows="projects" :columns="columns" row-key="id" flat bordered :loading="loading"
          :pagination="{ rowsPerPage: 10 }" no-data-label="Sem registros">
          <template #body-cell-created_at="props">
            <q-td :props="props">
              {{ formatDate(props.row.created_at) }}
            </q-td>
          </template>
        </q-table>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { listProjects, createProject } from './api/projects';
import type { Project, ProjectStatus } from './api/projects';
import { Notify } from 'quasar';

const projects = ref<Project[]>([])
const loading = ref(false)
const name = ref<string>('')
const status = ref<ProjectStatus>('active')

const statusOption = [
  { label: 'Ativo', value: 'active' },
  { label: 'Pausado', value: 'paused' },
  { label: 'Concluído', value: 'done' },
]

const columns = [
  { name: 'id', label: 'ID', field: 'id', align: 'left', sortable: true },
  { name: 'name', label: 'Nome', field: 'name', align: 'left', sortable: true },
  { name: 'status', label: 'Status', field: 'status', align: 'left', sortable: true },
  { name: 'created_at', label: 'Criado em', field: 'created_at', align: 'left', sortable: true }
]

function formatDate(iso: string): string {
  const d = new Date(iso)
  return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(d)
}

const load = async () => {
  loading.value = true

  try {
    const data = await listProjects()
    projects.value =
      Array.isArray(data) ? data
      : (typeof data === 'string' && data.trim() ? JSON.parse(data) : [])
  }
  catch(e) {

  } finally {
    console.log(projects.value);
    
  }
  
  loading.value = false  
}

const add = async () => {
  if (!name.value.trim()) {
    Notify.create({ type: 'warning', message: 'Informe o nome' })
    return
  }
  console.log(name.value, status.value);
  
  await createProject({ name: name.value, status: status.value })
  name.value = ''
  status.value = 'active'
  await load()
}

onMounted(async () => {
  await load()
})
</script>