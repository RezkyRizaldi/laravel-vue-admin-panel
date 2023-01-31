<template>
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Users</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item">
							<RouterLink to="/admin/dashboard">Home</RouterLink>
						</li>
						<li class="breadcrumb-item active">Users</li>
					</ol>
				</div>
			</div>
		</div>
	</div>
	<div class="content">
		<div class="container-fluid">
			<div class="d-flex justify-content-between mb-2">
				<div class="d-flex">
					<button type="button" class="btn btn-primary" @click="createUser">
						<i class="fa fa-plus-circle mr-1"></i>
						Add New User
					</button>
					<div
						class="d-flex align-items-center"
						v-if="selectedUsers.length > 0"
					>
						<button
							type="button"
							class="ml-2 btn btn-danger"
							@click="bulkDeleteUser"
						>
							<i class="fa fa-trash mr-1"></i>
							Delete Selected
						</button>
						<span class="ml-2">Selected {{ selectedUsers.length }} users</span>
					</div>
				</div>
				<div>
					<input
						type="search"
						class="form-control"
						aria-label="Search"
						placeholder="Search"
						v-model="searchQuery"
					/>
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th style="width: 10px">
									<input
										type="checkbox"
										@change="selectAllUsers"
										v-model="selectAll"
										aria-label="Select All"
									/>
								</th>
								<th style="width: 10px">No.</th>
								<th>Name</th>
								<th>Email</th>
								<th>Registered Date</th>
								<th>Role</th>
								<th>Options</th>
							</tr>
						</thead>
						<tbody v-if="users.data.length > 0">
							<UserListItem
								v-for="(user, index) in users.data"
								:key="user.id"
								:user="user"
								:index="index"
								@editUser="editUser"
								@userDeleted="userDeleted"
								@toggleSelection="toggleSelection"
								:selectAll="selectAll"
							/>
						</tbody>
						<tbody v-else>
							<tr>
								<td colspan="7" class="text-center">No results found.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<Bootstrap4Pagination :data="users" @paginationChangePage="getUsers" />
		</div>
	</div>
	<div
		class="modal fade"
		id="userModal"
		data-backdrop="static"
		tabindex="-1"
		role="dialog"
		aria-labelledby="userModalLabel"
		aria-hidden="true"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="userModalLabel">
						<span v-if="editing">Edit User</span>
						<span v-else>Add New User</span>
					</h5>
					<button
						type="button"
						class="close"
						data-dismiss="modal"
						aria-label="Close"
					>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<Form
					@submit="handleSubmit"
					:validationSchema="editing ? editUserSchema : createUserSchema"
					v-slot="{ errors }"
					:initialValues="formValues"
				>
					<div class="modal-body">
						<div class="form-group">
							<label for="name">Name</label>
							<Field
								type="text"
								class="form-control"
								:class="{ 'is-invalid': errors.name }"
								id="name"
								placeholder="Enter full name"
								name="name"
							/>
							<span class="invalid-feedback">{{ errors.name }}</span>
						</div>
						<div class="form-group">
							<label for="email">Email</label>
							<Field
								type="email"
								class="form-control"
								:class="{ 'is-invalid': errors.email }"
								id="email"
								placeholder="Enter Email"
								name="email"
							/>
							<span class="invalid-feedback">{{ errors.email }}</span>
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<Field
								type="password"
								class="form-control"
								:class="{ 'is-invalid': errors.password }"
								id="password"
								placeholder="Enter password"
								name="password"
							/>
							<span class="invalid-feedback">{{ errors.password }}</span>
						</div>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary"
							data-dismiss="modal"
						>
							Cancel
						</button>
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</Form>
			</div>
		</div>
	</div>
</template>

<script setup>
import { Bootstrap4Pagination } from "laravel-vue-pagination";
import { debounce } from "lodash";
import { Form, Field } from "vee-validate";
import { onMounted, ref, watch } from "vue";
import { object, string } from "yup";

import UserListItem from "./UserListItem.vue";
import { useToastr } from "../../toastr";

const users = ref({ data: [] });
const editing = ref(false);
const formValues = ref({});
const searchQuery = ref("");
const selectedUsers = ref([]);
const selectAll = ref(false);

const toastr = useToastr();

const createUserSchema = object({
	name: string().required(),
	email: string().required().email(),
	password: string().required().min(8),
});
const editUserSchema = object({
	name: string().required(),
	email: string().required().email(),
	password: string().when((password, schema) =>
		password ? schema.required().min(8) : schema
	),
});

const getUsers = async (page = 1) => {
	try {
		const { data } = await axios.get(`/api/users?page=${page}`);

		users.value = data;
		selectedUsers.value = [];
		selectAll.value = false;
	} catch (error) {
		toastr.error(error);
	}
};

const createUser = () => {
	editing.value = false;

	$("#userModal").modal("show");

	formValues.value = {};
};

const storeUser = async (values, { resetForm, setErrors }) => {
	try {
		const { data } = await axios.post("/api/users", values);

		users.value.data.unshift(data);
		resetForm();
		$("#userModal").modal("hide");
		toastr.success("User created successfully!");
	} catch (error) {
		if (error.response.data.errors) {
			setErrors(error.response.data.errors);
		} else {
			toastr.error(error);
		}
	}
};

const editUser = (user) => {
	editing.value = true;

	$("#userModal").modal("show");

	formValues.value = {
		id: user.id,
		name: user.name,
		email: user.email,
	};
};

const updateUser = async (values, { resetForm, setErrors }) => {
	try {
		const { data } = await axios.put(
			`/api/users/${formValues.value.id}`,
			values
		);

		const idx = users.value.data.findIndex((user) => user.id === data.id);
		users.value.data[idx] = data;

		resetForm();
		$("#userModal").modal("hide");
		toastr.success("User updated successfully!");
	} catch (error) {
		if (error.response.data.errors) {
			setErrors(error.response.data.errors);
		} else {
			toastr.error(error);
		}
	}
};

const handleSubmit = (values, actions) => {
	if (editing.value) {
		updateUser(values, actions);
	} else {
		storeUser(values, actions);
	}
};

const userDeleted = (userId) => {
	users.value.data = users.value.data.filter((user) => user.id !== userId);
};

const searchUser = async () => {
	try {
		const { data } = await axios.get("/api/users/search", {
			params: { query: searchQuery.value },
		});

		users.value.data = data;
	} catch (error) {
		console.error(error);
	}
};

const toggleSelection = (user) => {
	const index = selectedUsers.value.indexOf(user.id);

	if (index === -1) {
		selectedUsers.value.push(user.id);
	} else {
		selectedUsers.value.splice(index, 1);
	}
};

const bulkDeleteUser = async () => {
	try {
		await axios.delete("/api/users", { data: { ids: selectedUsers.value } });

		users.value.data = users.value.data.filter(
			(user) => !selectedUsers.value.includes(user.id)
		);
		selectedUsers.value = [];
		selectAll.value = false;

		toastr.success("Users deleted successfully!");
	} catch (error) {
		toastr.error(error);
	}
};

const selectAllUsers = () => {
	if (selectAll.value) {
		selectedUsers.value = users.value.data.map((user) => user.id);
	} else {
		selectedUsers.value = [];
	}
};

watch(
	searchQuery,
	debounce(() => {
		searchUser();
	}, 300)
);

onMounted(() => {
	getUsers();
});
</script>
