<template>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Create Appointment</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item">
							<RouterLink to="/admin/dashboard">Home</RouterLink>
						</li>
						<li class="breadcrumb-item">
							<RouterLink to="/admin/appointments">Appointments</RouterLink>
						</li>
						<li class="breadcrumb-item active">Create</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<form @submit.prevent="handleSubmit">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="title">Title</label>
											<input
												v-model="form.title"
												type="text"
												class="form-control"
												:class="{ 'is-invalid': errors.title }"
												id="title"
												placeholder="Enter Title"
											/>
											<span class="invalid-feedback">{{ errors.title }}</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="client">Client Name</label>
											<select
												v-model="form.client_id"
												id="client"
												class="form-control"
												:class="{ 'is-invalid': errors.client_id }"
											>
												<option
													v-for="client in clients"
													:key="client.id"
													:value="client.id"
												>
													{{ client.first_name }} {{ client.last_name }}
												</option>
											</select>
											<span class="invalid-feedback">{{
												errors.client_id
											}}</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="startTime">Start Time</label>
											<input
												v-model="form.start_time"
												type="text"
												class="form-control flatpickr"
												:class="{ 'is-invalid': errors.start_time }"
												id="startTime"
											/>
											<span class="invalid-feedback">{{
												errors.start_time
											}}</span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="endTime">End Time</label>
											<input
												v-model="form.end_time"
												type="text"
												class="form-control flatpickr"
												:class="{ 'is-invalid': errors.end_time }"
												id="endTime"
											/>
											<span class="invalid-feedback">{{
												errors.end_time
											}}</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="description">Description</label>
									<textarea
										v-model="form.description"
										class="form-control"
										:class="{ 'is-invalid': errors.description }"
										id="description"
										rows="3"
										placeholder="Enter Description"
									></textarea>
									<span class="invalid-feedback">{{ errors.description }}</span>
								</div>
								<button type="submit" class="btn btn-primary">Submit</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import flatpickr from "flatpickr";
import "flatpickr/dist/themes/light.css";
import { onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";

import { useToastr } from "../../toastr";

const clients = ref([]);

const form = reactive({
	title: "",
	client_id: "",
	start_time: "",
	end_time: "",
	description: "",
});

const router = useRouter();
const toastr = useToastr();

const handleSubmit = async () => {
	try {
		await axios.post("/api/appointments/store", form);

		await router.push("/admin/appointments");

		toastr.success("Appointment created successfully!");
	} catch (error) {
		console.error(error);
	}
};

const getClients = async () => {
	try {
		const { data } = await axios.get("/api/clients");

		clients.value = data;
	} catch (error) {
		console.error(error);
	}
};

onMounted(() => {
	flatpickr(".flatpickr", {
		enableTime: true,
		dateFormat: "Y-m-d h:i K",
		defaultHour: 10,
	});
	getClients();
});
</script>
