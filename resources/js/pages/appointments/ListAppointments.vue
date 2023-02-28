<template>
	<div>
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0">Appointments</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item">
								<RouterLink to="/admin/dashboard">Home</RouterLink>
							</li>
							<li class="breadcrumb-item active">Appointments</li>
						</ol>
					</div>
				</div>
			</div>
		</div>
		<div class="content">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<div class="d-flex justify-content-between mb-2">
							<div>
								<RouterLink to="/admin/appointments/create">
									<button class="btn btn-primary">
										<i class="fa fa-plus-circle mr-1"></i> Add New Appointment
									</button>
								</RouterLink>
							</div>
							<div class="btn-group">
								<button
									type="button"
									class="btn"
									:class="[
										typeof selectedStatus === 'undefined'
											? 'btn-secondary'
											: 'btn-default',
									]"
									@click="getAppointments()"
								>
									<span class="mr-1">All</span>
									<span class="badge badge-pill badge-info">{{
										appointmentsCount
									}}</span>
								</button>
								<button
									type="button"
									class="btn"
									:class="[
										selectedStatus === status.value
											? 'btn-secondary'
											: 'btn-default',
									]"
									v-for="status in appointmentStatus"
									@click="getAppointments(status.value)"
								>
									<span class="mr-1">{{ status.name }}</span>
									<span :class="`badge badge-pill badge-${status.color}`">{{
										status.count
									}}</span>
								</button>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Client Name</th>
											<th scope="col">Date</th>
											<th scope="col">Time</th>
											<th scope="col">Status</th>
											<th scope="col">Options</th>
										</tr>
									</thead>
									<tbody>
										<tr
											v-for="(appointment, index) in appointments.data"
											:key="appointment.id"
										>
											<td>{{ index + 1 }}</td>
											<td>
												{{ appointment.client.first_name }}
												{{ appointment.client.last_name }}
											</td>
											<td>{{ appointment.start_time }}</td>
											<td>{{ appointment.end_time }}</td>
											<td>
												<span
													class="badge"
													:class="`badge-${appointment.status.color}`"
													>{{ appointment.status.name }}</span
												>
											</td>
											<td>
												<a href="#">
													<span class="sr-only">Edit</span>
													<i class="fa fa-edit mr-2"></i>
												</a>
												<a href="#">
													<span class="sr-only">Delete</span>
													<i class="fa fa-trash text-danger"></i>
												</a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";

const appointments = ref([]);
const appointmentStatus = ref([]);
const selectedStatus = ref();

const getAppointmentStatus = async () => {
	try {
		const { data } = await axios.get("/api/appointment-status");

		appointmentStatus.value = data;
	} catch (error) {
		console.error(error);
	}
};

const getAppointments = async (status) => {
	selectedStatus.value = status;

	const params = {};

	if (status) {
		params.status = status;
	}

	try {
		const { data } = await axios.get("/api/appointments", { params });

		appointments.value = data;
	} catch (error) {
		console.error(error);
	}
};

const appointmentsCount = computed(() =>
	appointmentStatus.value
		.map((status) => status.count)
		.reduce((acc, curr) => acc + curr, 0)
);

onMounted(() => {
	getAppointments();
	getAppointmentStatus();
});
</script>
