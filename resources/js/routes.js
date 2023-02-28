import Dashboard from "./components/Dashboard.vue";
import AppointmentForm from "./pages/appointments/AppointmentForm.vue";
import ListAppointments from "./pages/appointments/ListAppointments.vue";
import UpdateProfile from "./pages/profile/UpdateProfile.vue";
import UpdateSetting from "./pages/settings/UpdateSetting.vue";
import UserList from "./pages/users/UserList.vue";

/** @type {import('vue-router').RouteRecordRaw[]} */
export default [
	{
		path: "/admin/dashboard",
		name: "admin.dashboard",
		component: Dashboard,
	},
	{
		path: "/admin/appointments",
		name: "admin.appointments",
		component: ListAppointments,
	},
	{
		path: "/admin/appointments/create",
		name: "admin.appointments.create",
		component: AppointmentForm,
	},
	{
		path: "/admin/users",
		name: "admin.users",
		component: UserList,
	},
	{
		path: "/admin/settings",
		name: "admin.settings",
		component: UpdateSetting,
	},
	{
		path: "/admin/profile",
		name: "admin.profile",
		component: UpdateProfile,
	},
];
