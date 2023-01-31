<template>
	<tr>
		<td>
			<input
				type="checkbox"
				@change="toggleSelection"
				:checked="selectAll"
				aria-label="Bulk Delete"
			/>
		</td>
		<td>{{ index + 1 }}.</td>
		<td>{{ user.name }}</td>
		<td>{{ user.email }}</td>
		<td>{{ user.formatted_created_at }}</td>
		<td>
			<select
				class="form-control"
				@change="changeRole(user, $event.target.value)"
			>
				<option
					v-for="role in roles"
					:value="role.value"
					:selected="user.role === role.name"
				>
					{{ role.name }}
				</option>
			</select>
		</td>
		<td>
			<button
				class="btn btn-info"
				type="button"
				@click.prevent="editUser(user)"
			>
				<i class="fa fa-edit"></i>
			</button>
			<button
				class="btn btn-danger ml-2"
				type="button"
				@click.prevent="deleteUser(user)"
			>
				<i class="fa fa-trash"></i>
			</button>
		</td>
	</tr>
	<div
		class="modal fade"
		id="userDeleteModal"
		data-backdrop="static"
		tabindex="-1"
		role="dialog"
		aria-labelledby="userDeleteModalLabel"
		aria-hidden="true"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="userDeleteModalLabel">Delete User</h5>
					<button
						type="button"
						class="close"
						data-dismiss="modal"
						aria-label="Close"
					>
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<h5>Are you sure you want to delete this user?</h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						Cancel
					</button>
					<button
						type="button"
						class="btn btn-danger"
						@click.prevent="destroyUser"
					>
						Delete
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref } from "vue";

import { useToastr } from "../../toastr";

const props = defineProps({
	user: { type: Object, required: true },
	index: { type: Number, required: true },
	selectAll: { type: Boolean, required: true },
});

const emit = defineEmits(["userDeleted", "editUser", "toggleSelection"]);

const userId = ref(null);
const roles = ref([
	{
		name: "ADMIN",
		value: 1,
	},
	{
		name: "USER",
		value: 2,
	},
]);

const toastr = useToastr();

const editUser = (user) => {
	emit("editUser", user);
};

const deleteUser = (user) => {
	userId.value = user.id;
	$("#userDeleteModal").modal("show");
};

const destroyUser = async () => {
	try {
		await axios.delete(`/api/users/${userId.value}`);

		$("#userDeleteModal").modal("hide");
		toastr.success("User deleted successfully!");
		emit("userDeleted", userId.value);
	} catch (error) {
		toastr.error(error);
	}
};

const changeRole = async (user, role) => {
	try {
		await axios.patch(`/api/users/${user.id}/change-role`, { role });

		toastr.success("Role changed successfully!");
	} catch (error) {
		toastr.error(error);
	}
};

const toggleSelection = () => {
	emit("toggleSelection", props.user);
};
</script>
