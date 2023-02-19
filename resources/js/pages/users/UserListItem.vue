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
				@click.prevent="$emit('editUser', user)"
			>
				<i class="fa fa-edit"></i>
			</button>
			<button
				class="btn btn-danger ml-2"
				type="button"
				@click.prevent="$emit('deleteUser', user.id)"
			>
				<i class="fa fa-trash"></i>
			</button>
		</td>
	</tr>
</template>

<script setup>
import { ref } from "vue";
import { useToastr } from "../../toastr";

const props = defineProps({
	user: { type: Object, required: true },
	index: { type: Number, required: true },
	selectAll: { type: Boolean, required: true },
});

const emit = defineEmits(["editUser", "deleteUser", "toggleSelection"]);

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
