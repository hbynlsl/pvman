<template>
	<div id="app">
		<h1>Hello App!</h1>
		<p><strong>Current route path:</strong> {{ $route.fullPath }}</p>
		<nav>
			<RouterLink to="/">Go to Home</RouterLink>
			<RouterLink to="/about">Go to About</RouterLink>

			<template v-if="authStore.isLoggedIn">
				<span class="user-greeting">👋 {{ authStore.user?.username || authStore.user?.name || '用户' }}</span>
				<var-button size="small" type="danger" @click="handleLogout">注销</var-button>
			</template>
			<template v-else>
				<RouterLink to="/login">Go to Login</RouterLink>
			</template>
		</nav>
		<main>
			<RouterView />
		</main>
	</div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

function handleLogout() {
	authStore.logout()
	router.push('/login')
}
</script>

<style scoped>
nav {
	display: flex;
	align-items: center;
	gap: 12px;
	margin: 12px 0;
}
.user-greeting {
	font-weight: 500;
	color: #2c3e50;
}
</style>
