<template>
	<div class="login-page">
		<var-space direction="column" align="center" :size="20">
			<h2>用户登录</h2>

			<var-form ref="formRef" @submit="handleLogin">
				<var-space direction="column" :size="16">
					<var-input
						v-model="username"
						placeholder="请输入用户名"
						:rules="[(v) => !!v || '用户名不能为空']"
						clearable
					>
						<template #prepend-icon>
							<var-icon name="account-circle" />
						</template>
					</var-input>

					<var-input
						v-model="password"
						type="password"
						placeholder="请输入密码"
						:rules="[(v) => !!v || '密码不能为空']"
						clearable
					>
						<template #prepend-icon>
							<var-icon name="lock" />
						</template>
					</var-input>

					<var-button
						type="primary"
						block
						:loading="loading"
						loading-type="circle"
						native-type="submit"
					>
						{{ loading ? "登录中..." : "登 录" }}
					</var-button>

					<var-alert v-if="errorMsg" type="danger">
						{{ errorMsg }}
					</var-alert>
				</var-space>
			</var-form>
		</var-space>
	</div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const formRef = ref(null);
const username = ref("");
const password = ref("");
const loading = ref(false);
const errorMsg = ref("");

async function handleLogin() {
	errorMsg.value = "";

	try {
		// 表单校验
		const valid = await formRef.value.validate();
		if (!valid) return;

		loading.value = true;

		const res = await fetch("/api/v1/login", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({
				username: username.value,
				password: password.value,
			}),
		});

		const data = await res.json();

		if (data.code !== 0) {
			errorMsg.value = data.message || "登录失败";
			return;
		}

		// 保存登录状态
		authStore.login(
			data.data.user,
			data.data.token,
			data.data.isAdmin ?? false,
		);

		// 跳转到首页
		router.push("/");
	} catch (e) {
		errorMsg.value = "网络错误，请稍后重试";
	} finally {
		loading.value = false;
	}
}
</script>

<style scoped>
.login-page {
	max-width: 360px;
	margin: 80px auto 0;
	padding: 0 16px;
}
</style>
