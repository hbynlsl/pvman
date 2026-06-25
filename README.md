# pvman - php+vue开发模版

基于 **Webman** + **Vue 3** + **SQLite** 构建的高性能开发模版。

## 技术栈

| 层级 | 技术 | 说明 |
|------|------|------|
| **后端** | [Webman](https://www.workerman.net/doc/webman) | 基于 Workerman 的高性能 PHP HTTP 服务框架 |
| **数据库** | SQLite (默认) / MySQL / PostgreSQL | 通过 Laravel Illuminate Database 组件操作 |
| **ORM** | [Eloquent](https://laravel.com/docs/eloquent) 模型 | `app/model/` 目录 |
| **前端** | [Vue 3](https://vuejs.org/) + Composition API | SPA 单页应用 |
| **UI 库** | [Naive UI](https://www.naiveui.com/) | 按需引入组件 |
| **状态管理** | [Pinia](https://pinia.vuejs.org/) | `src/stores/` |
| **路由** | [Vue Router](https://router.vuejs.org/) + 自动路由 | `src/pages/` 文件系统自动生成 |
| **构建工具** | [Vite](https://vite.dev/) | 极速开发体验 |
| **包管理** | pnpm | 前端依赖管理 |
| **模板引擎** | [Blade](https://laravel.com/docs/blade) (可选) | 通过 `webman/blade` 支持 |

## 快速开始

### 环境要求

- PHP >= 8.1
- Node.js >= 20.19.0 || >= 22.12.0
- pnpm >= 11.7.0
- Composer

### 安装 & 启动

```bash
# 1. 安装后端依赖
composer install

# 2. 安装前端依赖
pnpm install

# 3. 启动后端服务（终端 1）
php webman start

# 4. 启动前端开发服务器（终端 2）
pnpm dev
```

前端开发服务器运行在 `http://localhost:5173`，API 请求自动代理到 `http://127.0.0.1:8787`。

### 构建前端

```bash
pnpm build
```

构建产物输出到 `public/` 目录，可直接由 Webman 静态托管。

## 项目结构

```
├── app/                    # 后端 PHP 应用
│   ├── controller/         # 控制器（API 接口）
│   │   ├── IndexController.php
│   │   └── ClassteamController.php
│   ├── middleware/          # 中间件
│   ├── model/               # Eloquent 模型
│   │   ├── Classteam.php
│   │   └── Test.php
│   ├── validation/          # 验证器
│   ├── command/             # 自定义控制台命令
│   ├── process/             # 自定义进程
│   └── view/                # Blade 视图模板
├── config/                  # 后端配置
│   ├── app.php              # 应用配置
│   ├── database.php         # 数据库配置（支持 mysql/sqlite/pgsql）
│   ├── route.php            # API 路由定义
│   ├── middleware.php        # 中间件配置
│   └── ...
├── src/                     # 前端 Vue 3 应用
│   ├── App.vue              # 根组件
│   ├── main.js              # 入口文件
│   ├── pages/               # 页面组件（自动路由）
│   │   ├── login.vue        # 登录页
│   │   ├── home/            # 普通用户页面
│   │   │   ├── index.vue
│   │   │   ├── chapters.vue
│   │   │   └── contents.vue
│   │   └── admin/           # 管理员页面
│   │       ├── index.vue
│   │       ├── users.vue
│   │       ├── books.vue
│   │       ├── chapters.vue
│   │       ├── contents.vue
│   │       └── labels.vue
│   ├── router/              # Vue Router（自动生成）
│   ├── stores/              # Pinia 状态管理
│   │   └── auth.js          # 认证状态
│   └── utils/               # 工具函数
├── public/                  # Webman 静态资源目录（构建输出）
├── vendor/                  # Composer 依赖
├── node_modules/            # pnpm 依赖
├── index.html               # HTML 入口
├── vite.config.js           # Vite 配置
├── composer.json
├── package.json
└── webman                   # Webman 启动入口
```

## API 路由

后端 API 统一以 `/api/v1/` 前缀注册，定义在 `config/route.php` 中。

| 方法 | 路径 | 控制器 | 说明 |
|------|------|--------|------|
| POST | `/api/v1/login` | IndexController@login | 用户登录 |
| POST | `/api/v1/classteam/create` | ClassteamController@create | 创建班级 |
| PUT  | `/api/v1/classteam/update` | ClassteamController@update | 更新班级 |
| DELETE | `/api/v1/classteam/delete` | ClassteamController@delete | 删除班级 |
| GET  | `/api/v1/classteam/detail` | ClassteamController@detail | 班级详情 |

所有未匹配的路由由 `fallback` 返回 `index.html`，交给 Vue Router 处理。

## 前端自动路由

`src/pages/` 目录下的文件由 `unplugin-vue-router` 自动扫描生成路由：

```
src/pages/login.vue        →  /login
src/pages/home/index.vue   →  /home
src/pages/home/chapters.vue→  /home/chapters
src/pages/home/contents.vue→  /home/contents
src/pages/admin/index.vue  →  /admin
...
```

## 数据库

默认使用 SQLite，数据库文件为项目根目录的 `test.db`。

如需切换为 MySQL 或 PostgreSQL，修改 `config/database.php` 中的 `default` 连接名及相关配置即可。

## Vite 配置要点

- **别名**：`@` → `src/`
- **代理**：`/api` → `http://127.0.0.1:8787`
- **构建输出**：`public/`（与 Webman 静态目录一致）
- **自动导入**：Vue API 和 Naive UI `useDialog` / `useMessage` / `useNotification` / `useLoadingBar`

## License

[MIT](LICENSE)
