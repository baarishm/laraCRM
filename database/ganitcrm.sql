-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2018 at 02:03 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ganitcrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `file_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `backup_size` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tags` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '[]',
  `color` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `tags`, `color`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Administration', '[]', '#000', NULL, '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(2, 'Development', '[]', '#fff', NULL, '2018-08-08 00:59:46', '2018-08-08 00:59:46');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `designation` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Male',
  `mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mobile2` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dept` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `city` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `about` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_birth` date NOT NULL DEFAULT '1990-01-01',
  `date_hire` date NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_approver` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `second_approver` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `designation`, `gender`, `mobile`, `mobile2`, `email`, `dept`, `city`, `address`, `about`, `date_birth`, `date_hire`, `deleted_at`, `created_at`, `updated_at`, `first_approver`, `second_approver`, `project_id`) VALUES
(1, 'Varsha', 'Super Admin', 'Female', '8888888888', '', 'varsha.mittal@ganitsoftech.com', 1, 'Pune', 'Karve nagar, Pune 411030', 'About user / biography', '2018-08-06', '2018-08-06', NULL, '2018-08-06 01:55:58', '2018-08-06 07:27:02', 1, 2, 1),
(2, 'Employee 2', 'Developer', 'Female', '9999999999', '', 'employee.2@ganitsoftech.com', 1, 'Gurgaon', '42/3, Sector 45', 'It is a fake id for testing', '1994-07-22', '2018-07-11', NULL, '2018-08-06 02:22:34', '2018-08-06 02:24:33', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `la_configs`
--

CREATE TABLE `la_configs` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `section` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `la_configs`
--

INSERT INTO `la_configs` (`id`, `key`, `section`, `value`, `created_at`, `updated_at`) VALUES
(1, 'sitename', '', 'Ganit CRM', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(2, 'sitename_part1', '', 'Ganit', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(3, 'sitename_part2', '', 'CRM', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(4, 'sitename_short', '', 'GC', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(5, 'site_description', '', 'CRM for Ganit Softech', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(6, 'sidebar_search', '', '1', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(7, 'show_messages', '', '0', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(8, 'show_notifications', '', '0', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(9, 'show_tasks', '', '0', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(10, 'show_rightsidebar', '', '0', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(11, 'skin', '', 'skin-white', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(12, 'layout', '', 'fixed', '2018-08-06 01:55:19', '2018-08-07 00:22:32'),
(13, 'default_email', '', 'varsha.mittal@ganitsoftech.com', '2018-08-06 01:55:19', '2018-08-07 00:22:32');

-- --------------------------------------------------------

--
-- Table structure for table `la_menus`
--

CREATE TABLE `la_menus` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fa-cube',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'module',
  `parent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `hierarchy` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `la_menus`
--

INSERT INTO `la_menus` (`id`, `name`, `url`, `icon`, `type`, `parent`, `hierarchy`, `created_at`, `updated_at`) VALUES
(1, 'Team', '#', 'fa-group', 'custom', 0, 4, '2018-08-06 01:55:16', '2018-08-13 05:33:51'),
(5, 'Employees', 'employees', 'fa-group', 'module', 1, 1, '2018-08-06 01:55:17', '2018-08-07 02:05:51'),
(14, 'Projects', 'projects', 'fa-folder', 'module', 0, 2, '2018-08-06 05:15:36', '2018-08-13 05:33:51'),
(17, 'Roles', 'roles', 'fa-user-plus', 'module', 1, 4, '2018-08-06 07:13:25', '2018-08-07 02:05:52'),
(19, 'Tasks', 'tasks', 'fa-file-o', 'module', 0, 3, '2018-08-06 07:13:35', '2018-08-13 05:33:51'),
(20, 'Leads', 'leads', 'fa fa-male', 'module', 1, 2, '2018-08-06 07:32:16', '2018-08-07 02:05:52'),
(21, 'Managers', 'managers', 'fa-male', 'module', 1, 3, '2018-08-06 07:34:17', '2018-08-07 02:05:52'),
(22, 'Create Project', 'projects/create', 'fa-folder-o', 'custom', 14, 1, '2018-08-07 02:05:44', '2018-08-07 02:06:40'),
(23, 'Existing Projects', 'projects', 'fa-folder-open-o', 'custom', 14, 2, '2018-08-07 02:07:15', '2018-08-07 02:25:59'),
(24, 'Create Task', 'tasks/create', 'fa-file-text-o', 'custom', 19, 1, '2018-08-07 02:12:57', '2018-08-07 02:13:39'),
(25, 'Existing Tasks', 'tasks', 'fa-file-text-o', 'custom', 19, 2, '2018-08-07 02:14:01', '2018-08-07 02:14:07'),
(26, 'Create Employee', 'employees/create', 'fa-group', 'custom', 5, 1, '2018-08-07 02:18:41', '2018-08-07 02:19:43'),
(27, 'Existing Employees', 'employees', 'fa-group', 'custom', 5, 2, '2018-08-07 02:19:32', '2018-08-07 02:19:36'),
(28, 'Create Lead', 'leads/create', 'fa-group', 'custom', 20, 1, '2018-08-07 02:23:03', '2018-08-07 02:23:10'),
(29, 'Existing Leads', 'leads', 'fa-group', 'custom', 20, 2, '2018-08-07 02:23:27', '2018-08-07 02:23:30'),
(30, 'Create Manager', 'managers/create', 'fa-group', 'custom', 21, 1, '2018-08-07 02:25:32', '2018-08-07 02:26:09'),
(31, 'Existing Managers', 'managers', 'fa-group', 'custom', 21, 2, '2018-08-07 02:25:50', '2018-08-07 02:26:12'),
(32, 'Create Role', 'roles/create', 'fa-user-plus', 'custom', 17, 1, '2018-08-07 02:44:29', '2018-08-07 02:44:38'),
(33, 'Existing Roles', 'roles', 'fa-user-plus', 'custom', 17, 2, '2018-08-07 02:44:57', '2018-08-07 02:45:01'),
(40, 'Timesheets', 'timesheets', 'fa-clock-o', 'module', 0, 1, '2018-08-08 06:13:27', '2018-08-10 05:36:25'),
(41, 'Add Timesheet', 'timesheets/create', 'fa-clock-o', 'custom', 40, 1, '2018-08-08 06:13:57', '2018-08-08 06:14:31'),
(42, 'You Recent Timesheets', 'timesheets', 'fa-clock-o', 'custom', 40, 2, '2018-08-08 06:14:23', '2018-08-08 06:14:34'),
(48, 'Sidebar_Menu_Accesses', 'sidebar_menu_accesses', 'fa fa-link', 'module', 0, 5, '2018-08-13 05:28:33', '2018-08-13 05:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `deleted_at`, `created_at`, `updated_at`, `employee_id`) VALUES
(1, NULL, '2018-08-06 07:33:54', '2018-08-06 07:33:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `managers`
--

INSERT INTO `managers` (`id`, `deleted_at`, `created_at`, `updated_at`, `employee_id`) VALUES
(1, NULL, '2018-08-06 07:34:56', '2018-08-06 07:34:56', 2);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_05_26_050000_create_modules_table', 1),
('2014_05_26_055000_create_module_field_types_table', 1),
('2014_05_26_060000_create_module_fields_table', 1),
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2014_12_01_000000_create_uploads_table', 1),
('2016_05_26_064006_create_departments_table', 1),
('2016_05_26_064007_create_employees_table', 1),
('2016_05_26_064446_create_roles_table', 1),
('2016_07_05_115343_create_role_user_table', 1),
('2016_07_06_140637_create_organizations_table', 1),
('2016_07_07_134058_create_backups_table', 1),
('2016_07_07_134058_create_menus_table', 1),
('2016_09_10_163337_create_permissions_table', 1),
('2016_09_10_163520_create_permission_role_table', 1),
('2016_09_22_105958_role_module_fields_table', 1),
('2016_09_22_110008_role_module_table', 1),
('2016_10_06_115413_create_la_configs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name_db` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `view_col` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `controller` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fa_icon` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fa-cube',
  `is_gen` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `label`, `name_db`, `view_col`, `model`, `controller`, `fa_icon`, `is_gen`, `created_at`, `updated_at`) VALUES
(1, 'Users', 'Users', 'users', 'name', 'User', 'UsersController', 'fa-group', 1, '2018-08-06 01:54:58', '2018-08-06 01:55:19'),
(2, 'Uploads', 'Uploads', 'uploads', 'name', 'Upload', 'UploadsController', 'fa-files-o', 1, '2018-08-06 01:54:59', '2018-08-06 07:22:24'),
(3, 'Departments', 'Departments', 'departments', 'name', 'Department', 'DepartmentsController', 'fa-tags', 1, '2018-08-06 01:55:00', '2018-08-06 01:55:19'),
(4, 'Employees', 'Employees', 'employees', 'name', 'Employee', 'EmployeesController', 'fa-group', 1, '2018-08-06 01:55:01', '2018-08-06 07:20:23'),
(5, 'Roles', 'Roles', 'roles', 'name', 'Role', 'RolesController', 'fa-user-plus', 1, '2018-08-06 01:55:02', '2018-08-06 01:55:19'),
(6, 'Organizations', 'Organizations', 'organizations', 'name', 'Organization', 'OrganizationsController', 'fa-university', 1, '2018-08-06 01:55:07', '2018-08-06 01:55:19'),
(7, 'Backups', 'Backups', 'backups', 'name', 'Backup', 'BackupsController', 'fa-hdd-o', 1, '2018-08-06 01:55:09', '2018-08-06 01:55:19'),
(8, 'Permissions', 'Permissions', 'permissions', 'name', 'Permission', 'PermissionsController', 'fa-magic', 1, '2018-08-06 01:55:10', '2018-08-06 01:55:19'),
(9, 'Projects', 'Projects', 'projects', 'name', 'Project', 'ProjectsController', 'fa-folder', 1, '2018-08-06 02:33:15', '2018-08-06 04:10:48'),
(10, 'Tasks', 'Tasks', 'tasks', 'name', 'Task', 'TasksController', 'fa-file-o', 1, '2018-08-06 04:18:36', '2018-08-06 04:21:34'),
(11, 'Leads', 'Leads', 'leads', 'employee_id', 'Lead', 'LeadsController', 'fa-male', 1, '2018-08-06 07:31:37', '2018-08-06 07:32:16'),
(12, 'Managers', 'Managers', 'managers', 'employee_id', 'Manager', 'ManagersController', 'fa-male', 1, '2018-08-06 07:33:19', '2018-08-06 07:34:46'),
(13, 'Timesheets', 'Timesheets', 'timesheets', 'project_id', 'Timesheet', 'TimesheetsController', 'fa-clock-o', 1, '2018-08-08 00:58:00', '2018-08-08 01:28:25'),
(15, 'Role_menus', 'Role_menus', 'role_menus', 'role_id', 'Role_menu', 'Role_menusController', 'fa-angle-double-down', 0, '2018-08-10 05:47:25', '2018-08-10 05:49:47'),
(17, 'RoleMenus', 'RoleMenus', 'rolemenus', 'role_id', 'RoleMenu', 'RoleMenusController', 'fa-list-ol', 0, '2018-08-13 02:52:07', '2018-08-13 02:52:44'),
(19, 'Sidebar_Menu_Accesses', 'Sidebar_Menu_Accesses', 'sidebar_menu_accesses', 'role_id', 'Sidebar_Menu_Access', 'Sidebar_Menu_AccessesController', 'fa-link', 1, '2018-08-13 05:27:35', '2018-08-13 05:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `module_fields`
--

CREATE TABLE `module_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `colname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `module` int(10) UNSIGNED NOT NULL,
  `field_type` int(10) UNSIGNED NOT NULL,
  `unique` tinyint(1) NOT NULL DEFAULT '0',
  `defaultvalue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `minlength` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `maxlength` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `popup_vals` text COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `module_fields`
--

INSERT INTO `module_fields` (`id`, `colname`, `label`, `module`, `field_type`, `unique`, `defaultvalue`, `minlength`, `maxlength`, `required`, `popup_vals`, `sort`, `created_at`, `updated_at`) VALUES
(1, 'name', 'Name', 1, 16, 0, '', 5, 250, 1, '', 0, '2018-08-06 01:54:58', '2018-08-06 01:54:58'),
(2, 'context_id', 'Context', 1, 13, 0, '0', 0, 0, 0, '', 0, '2018-08-06 01:54:58', '2018-08-06 01:54:58'),
(3, 'email', 'Email', 1, 8, 1, '', 0, 250, 0, '', 0, '2018-08-06 01:54:58', '2018-08-06 01:54:58'),
(4, 'password', 'Password', 1, 17, 0, '', 6, 250, 1, '', 0, '2018-08-06 01:54:58', '2018-08-06 01:54:58'),
(5, 'type', 'User Type', 1, 7, 0, 'Employee', 0, 0, 0, '[\"Employee\",\"Client\"]', 0, '2018-08-06 01:54:58', '2018-08-06 01:54:58'),
(6, 'name', 'Name', 2, 16, 0, '', 5, 250, 1, '', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(7, 'path', 'Path', 2, 19, 0, '', 0, 250, 0, '', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(8, 'extension', 'Extension', 2, 19, 0, '', 0, 20, 0, '', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(9, 'caption', 'Caption', 2, 19, 0, '', 0, 250, 0, '', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(10, 'user_id', 'Owner', 2, 7, 0, '1', 0, 0, 0, '@users', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(11, 'hash', 'Hash', 2, 19, 0, '', 0, 250, 0, '', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(12, 'public', 'Is Public', 2, 2, 0, '0', 0, 0, 0, '', 0, '2018-08-06 01:54:59', '2018-08-06 01:54:59'),
(16, 'name', 'Name', 4, 16, 0, '', 5, 250, 1, '', 1, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(17, 'designation', 'Designation', 4, 19, 0, '', 0, 50, 1, '', 2, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(18, 'gender', 'Gender', 4, 18, 0, 'Male', 0, 0, 1, '[\"Male\",\"Female\"]', 3, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(19, 'mobile', 'Mobile', 4, 14, 0, '', 10, 20, 1, '', 4, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(20, 'mobile2', 'Alternative Mobile', 4, 14, 0, '', 10, 20, 0, '', 5, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(21, 'email', 'Email', 4, 8, 1, '', 5, 250, 1, '', 6, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(22, 'dept', 'Department', 4, 7, 0, '0', 0, 0, 1, '@departments', 13, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(23, 'city', 'City', 4, 19, 0, '', 0, 50, 0, '', 8, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(24, 'address', 'Address', 4, 1, 0, '', 0, 1000, 0, '', 9, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(25, 'about', 'About', 4, 19, 0, '', 0, 0, 0, '', 10, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(26, 'date_birth', 'Date of Birth', 4, 4, 0, '1990-01-01', 0, 0, 0, '', 7, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(27, 'date_hire', 'Hiring Date', 4, 4, 0, 'date(\'Y-m-d\')', 0, 0, 0, '', 16, '2018-08-06 01:55:01', '2018-08-06 01:55:01'),
(30, 'name', 'Name', 5, 16, 1, '', 1, 250, 1, '', 0, '2018-08-06 01:55:02', '2018-08-06 01:55:02'),
(31, 'display_name', 'Display Name', 5, 19, 0, '', 0, 250, 1, '', 0, '2018-08-06 01:55:02', '2018-08-06 01:55:02'),
(32, 'description', 'Description', 5, 21, 0, '', 0, 1000, 0, '', 0, '2018-08-06 01:55:02', '2018-08-06 01:55:02'),
(33, 'parent', 'Parent Role', 5, 7, 0, '1', 0, 0, 0, '@roles', 0, '2018-08-06 01:55:02', '2018-08-06 01:55:02'),
(34, 'dept', 'Department', 5, 7, 0, '1', 0, 0, 0, '@departments', 0, '2018-08-06 01:55:02', '2018-08-06 01:55:02'),
(35, 'name', 'Name', 6, 16, 1, '', 5, 250, 1, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(36, 'email', 'Email', 6, 8, 1, '', 0, 250, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(37, 'phone', 'Phone', 6, 14, 0, '', 0, 20, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(38, 'website', 'Website', 6, 23, 0, 'http://', 0, 250, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(39, 'assigned_to', 'Assigned to', 6, 7, 0, '0', 0, 0, 0, '@employees', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(40, 'connect_since', 'Connected Since', 6, 4, 0, 'date(\'Y-m-d\')', 0, 0, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(41, 'address', 'Address', 6, 1, 0, '', 0, 1000, 1, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(42, 'city', 'City', 6, 19, 0, '', 0, 250, 1, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(43, 'description', 'Description', 6, 21, 0, '', 0, 1000, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(44, 'profile_image', 'Profile Image', 6, 12, 0, '', 0, 250, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(45, 'profile', 'Company Profile', 6, 9, 0, '', 0, 250, 0, '', 0, '2018-08-06 01:55:07', '2018-08-06 01:55:07'),
(46, 'name', 'Name', 7, 16, 1, '', 0, 250, 1, '', 0, '2018-08-06 01:55:09', '2018-08-06 01:55:09'),
(47, 'file_name', 'File Name', 7, 19, 1, '', 0, 250, 1, '', 0, '2018-08-06 01:55:10', '2018-08-06 01:55:10'),
(48, 'backup_size', 'File Size', 7, 19, 0, '0', 0, 10, 1, '', 0, '2018-08-06 01:55:10', '2018-08-06 01:55:10'),
(49, 'name', 'Name', 8, 16, 1, '', 1, 250, 1, '', 0, '2018-08-06 01:55:10', '2018-08-06 01:55:10'),
(50, 'display_name', 'Display Name', 8, 19, 0, '', 0, 250, 1, '', 0, '2018-08-06 01:55:10', '2018-08-06 01:55:10'),
(51, 'description', 'Description', 8, 21, 0, '', 0, 1000, 0, '', 0, '2018-08-06 01:55:10', '2018-08-06 01:55:10'),
(52, 'name', 'Project Name', 9, 16, 0, 'Dummy', 5, 255, 1, '', 0, '2018-08-06 02:34:35', '2018-08-06 04:15:16'),
(53, 'client_id', 'Client', 9, 7, 0, '0', 0, 0, 1, '@organizations', 0, '2018-08-06 04:07:31', '2018-08-06 04:07:43'),
(54, 'start_date', 'Start Date', 9, 4, 0, '', 0, 0, 0, '', 0, '2018-08-06 04:08:59', '2018-08-06 04:21:57'),
(55, 'end_date', 'End Date', 9, 4, 0, '', 0, 0, 0, '', 0, '2018-08-06 04:10:07', '2018-08-06 04:10:07'),
(56, 'name', 'Task Name', 10, 16, 0, 'Dummy Task', 5, 255, 1, '', 0, '2018-08-06 04:20:32', '2018-08-06 04:20:32'),
(57, 'start_date', 'Start Date', 10, 4, 0, '', 0, 0, 0, '', 0, '2018-08-06 04:21:03', '2018-08-06 04:21:25'),
(58, 'end_date', 'End Date', 10, 4, 0, '', 0, 0, 0, '', 0, '2018-08-06 04:21:20', '2018-08-06 04:21:20'),
(59, 'first_approver', 'First Approver', 4, 7, 0, '0', 0, 0, 0, '@employees', 11, '2018-08-06 07:17:59', '2018-08-06 07:17:59'),
(60, 'second_approver', 'Second Approver', 4, 7, 0, '0', 0, 0, 0, '@employees', 12, '2018-08-06 07:18:23', '2018-08-06 07:18:23'),
(61, 'project_id', 'Project Name', 4, 7, 0, '0', 0, 0, 0, '@projects', 14, '2018-08-06 07:18:51', '2018-08-06 07:18:51'),
(62, 'employee_id', 'Lead Name', 11, 7, 0, '0', 0, 0, 1, '@employees', 0, '2018-08-06 07:32:11', '2018-08-06 07:32:11'),
(63, 'employee_id', 'Manager Name', 12, 7, 0, '0', 0, 0, 1, '@employees', 0, '2018-08-06 07:33:41', '2018-08-06 07:33:41'),
(64, 'project_id', 'Project Name', 13, 7, 0, '0', 0, 0, 1, '@projects', 2, '2018-08-08 01:01:24', '2018-08-08 01:01:24'),
(65, 'task_id', 'Task Name', 13, 7, 0, '0', 0, 0, 1, '@tasks', 3, '2018-08-08 01:01:45', '2018-08-08 01:01:45'),
(66, 'date', 'Date', 13, 4, 0, '', 0, 0, 1, '', 4, '2018-08-08 01:02:05', '2018-08-08 01:02:05'),
(67, 'hours', 'Hours Spent', 13, 7, 0, '0', 0, 0, 1, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 5, '2018-08-08 01:02:44', '2018-08-10 01:36:25'),
(68, 'minutes', 'Minutes Spent', 13, 7, 0, '0', 0, 0, 1, '[\"00\",\"30\"]', 6, '2018-08-08 01:03:14', '2018-08-08 01:03:14'),
(69, 'comments', 'Comment', 13, 21, 0, '', 0, 0, 0, '', 7, '2018-08-08 01:04:05', '2018-08-08 01:04:05'),
(70, 'dependency', 'Dependency', 13, 18, 0, 'no', 0, 0, 1, '[\"No\",\"Yes\"]', 8, '2018-08-08 01:04:47', '2018-08-09 01:24:16'),
(71, 'dependency_for', 'Dependency For', 13, 21, 0, '', 0, 0, 0, '', 9, '2018-08-08 01:05:09', '2018-08-08 01:05:09'),
(72, 'dependent_on', 'Dependent On', 13, 7, 0, '0', 0, 0, 0, '@employees', 10, '2018-08-08 01:05:35', '2018-08-08 04:28:37'),
(73, 'lead_id', 'Lead Name', 13, 7, 0, '0', 0, 0, 0, '@leads', 11, '2018-08-08 01:06:07', '2018-08-08 01:06:37'),
(74, 'manager_id', 'Manager Name', 13, 7, 0, '0', 0, 0, 0, '@managers', 12, '2018-08-08 01:06:27', '2018-08-08 01:06:27'),
(75, 'submitor_id', 'Submitor Name', 13, 7, 0, '0', 0, 0, 1, '@employees', 1, '2018-08-08 04:58:37', '2018-08-08 05:05:58'),
(86, 'role_id', 'Role Name', 19, 7, 0, '', 0, 0, 1, '@roles', 0, '2018-08-13 05:27:53', '2018-08-13 05:27:53'),
(87, 'menu_id', 'Menu Name', 19, 7, 0, '', 0, 0, 1, '@la_menus', 0, '2018-08-13 05:28:23', '2018-08-13 05:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `module_field_types`
--

CREATE TABLE `module_field_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `module_field_types`
--

INSERT INTO `module_field_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Address', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(2, 'Checkbox', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(3, 'Currency', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(4, 'Date', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(5, 'Datetime', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(6, 'Decimal', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(7, 'Dropdown', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(8, 'Email', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(9, 'File', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(10, 'Float', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(11, 'HTML', '2018-08-06 01:54:55', '2018-08-06 01:54:55'),
(12, 'Image', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(13, 'Integer', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(14, 'Mobile', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(15, 'Multiselect', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(16, 'Name', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(17, 'Password', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(18, 'Radio', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(19, 'String', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(20, 'Taginput', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(21, 'Textarea', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(22, 'TextField', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(23, 'URL', '2018-08-06 01:54:56', '2018-08-06 01:54:56'),
(24, 'Files', '2018-08-06 01:54:56', '2018-08-06 01:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'http://',
  `assigned_to` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `connect_since` date NOT NULL,
  `address` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `profile_image` int(11) NOT NULL,
  `profile` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `email`, `phone`, `website`, `assigned_to`, `connect_since`, `address`, `city`, `description`, `profile_image`, `profile`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Client 1', 'client@test.com', '9638527417', 'http://client.com', 1, '2018-08-06', 'Los Angeles', 'City', 'This is our first company', 0, 0, NULL, '2018-08-06 04:16:37', '2018-08-06 04:24:50');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ADMIN_PANEL', 'Admin Panel', 'Admin Panel Permission', NULL, '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(2, 'DASHBOARD_PERMISSION', 'Dash', 'asd', '2018-08-06 05:33:33', '2018-08-06 05:31:13', '2018-08-06 05:33:33'),
(3, 'sad', 'asd', 'asd', '2018-08-06 05:38:41', '2018-08-06 05:34:09', '2018-08-06 05:38:41'),
(4, 'Hey ', 'This is test permission', 'Not testing', NULL, '2018-08-06 05:39:00', '2018-08-06 05:39:00');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Dummy',
  `client_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `deleted_at`, `created_at`, `updated_at`, `name`, `client_id`, `start_date`, `end_date`) VALUES
(1, NULL, '2018-08-06 04:17:55', '2018-08-06 04:17:55', 'Client 1 ka first project', 1, '2018-08-06', '0000-00-00'),
(2, NULL, '2018-08-07 02:04:50', '2018-08-07 02:04:50', 'Project Via Create URL', 1, '2018-08-07', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `rolemenus`
--

CREATE TABLE `rolemenus` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `menu_id` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `display_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `dept` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `parent`, `dept`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'SUPER_ADMIN', 'Super Admin', 'Full Access Role', 1, 1, NULL, '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(2, 'ADMIN', 'ADMIN', 'Junior Admin', 2, 1, '2018-08-08 01:00:12', '2018-08-06 02:21:05', '2018-08-08 01:00:12'),
(3, 'EMPLOYEE', 'Employee', 'Employee in the company with Timesheet and Leave entry permission ', 3, 2, NULL, '2018-08-08 01:00:06', '2018-08-10 02:29:43');

-- --------------------------------------------------------

--
-- Table structure for table `role_menu`
--

CREATE TABLE `role_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_menu`
--

INSERT INTO `role_menu` (`id`, `role_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 4, NULL, NULL),
(3, 1, 5, NULL, NULL),
(4, 1, 14, NULL, NULL),
(5, 1, 17, NULL, NULL),
(6, 1, 18, NULL, NULL),
(7, 1, 19, NULL, NULL),
(8, 1, 20, NULL, NULL),
(9, 1, 21, NULL, NULL),
(10, 1, 22, NULL, NULL),
(11, 1, 23, NULL, NULL),
(12, 1, 24, NULL, NULL),
(13, 1, 25, NULL, NULL),
(14, 1, 26, NULL, NULL),
(15, 1, 27, NULL, NULL),
(16, 1, 28, NULL, NULL),
(17, 1, 29, NULL, NULL),
(18, 1, 30, NULL, NULL),
(19, 1, 31, NULL, NULL),
(20, 1, 32, NULL, NULL),
(21, 1, 33, NULL, NULL),
(22, 1, 34, NULL, NULL),
(23, 1, 35, NULL, NULL),
(24, 1, 36, NULL, NULL),
(25, 1, 37, NULL, NULL),
(26, 1, 40, NULL, NULL),
(27, 1, 41, NULL, NULL),
(28, 1, 42, NULL, NULL),
(29, 3, 40, NULL, NULL),
(30, 3, 41, NULL, NULL),
(31, 3, 42, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_module`
--

CREATE TABLE `role_module` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `module_id` int(10) UNSIGNED NOT NULL,
  `acc_view` tinyint(1) NOT NULL,
  `acc_create` tinyint(1) NOT NULL,
  `acc_edit` tinyint(1) NOT NULL,
  `acc_delete` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_module`
--

INSERT INTO `role_module` (`id`, `role_id`, `module_id`, `acc_view`, `acc_create`, `acc_edit`, `acc_delete`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(2, 1, 2, 1, 1, 1, 1, '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(3, 1, 3, 1, 1, 1, 1, '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(4, 1, 4, 1, 1, 1, 1, '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(5, 1, 5, 1, 1, 1, 1, '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(6, 1, 6, 1, 1, 1, 1, '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(7, 1, 7, 1, 1, 1, 1, '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(8, 1, 8, 1, 1, 1, 1, '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(17, 1, 9, 1, 1, 1, 1, '2018-08-06 04:10:40', '2018-08-06 04:10:40'),
(19, 1, 10, 1, 1, 1, 1, '2018-08-06 04:21:34', '2018-08-06 04:21:34'),
(20, 1, 11, 1, 1, 1, 1, '2018-08-06 07:32:16', '2018-08-06 07:32:16'),
(21, 1, 12, 1, 1, 1, 1, '2018-08-06 07:34:46', '2018-08-06 07:34:46'),
(34, 3, 13, 1, 1, 1, 1, '2018-08-08 01:00:08', '2018-08-08 01:00:08'),
(36, 1, 19, 1, 1, 1, 1, '2018-08-13 05:28:33', '2018-08-13 05:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `role_module_fields`
--

CREATE TABLE `role_module_fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `field_id` int(10) UNSIGNED NOT NULL,
  `access` enum('invisible','readonly','write') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_module_fields`
--

INSERT INTO `role_module_fields` (`id`, `role_id`, `field_id`, `access`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(2, 1, 2, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(3, 1, 3, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(4, 1, 4, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(5, 1, 5, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(6, 1, 6, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(7, 1, 7, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(8, 1, 8, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(9, 1, 9, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(10, 1, 10, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(11, 1, 11, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(12, 1, 12, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(16, 1, 16, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(17, 1, 17, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(18, 1, 18, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(19, 1, 19, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(20, 1, 20, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(21, 1, 21, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(22, 1, 22, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(23, 1, 23, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(24, 1, 24, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(25, 1, 25, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(26, 1, 26, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(27, 1, 27, 'write', '2018-08-06 01:55:17', '2018-08-06 01:55:17'),
(30, 1, 30, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(31, 1, 31, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(32, 1, 32, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(33, 1, 33, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(34, 1, 34, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(35, 1, 35, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(36, 1, 36, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(37, 1, 37, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(38, 1, 38, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(39, 1, 39, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(40, 1, 40, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(41, 1, 41, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(42, 1, 42, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(43, 1, 43, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(44, 1, 44, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(45, 1, 45, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(46, 1, 46, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(47, 1, 47, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(48, 1, 48, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(49, 1, 49, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(50, 1, 50, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(51, 1, 51, 'write', '2018-08-06 01:55:18', '2018-08-06 01:55:18'),
(52, 2, 1, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(53, 2, 2, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(54, 2, 3, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(55, 2, 4, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(56, 2, 5, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(57, 2, 6, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(58, 2, 7, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(59, 2, 8, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(60, 2, 9, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(61, 2, 10, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(62, 2, 11, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(63, 2, 12, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(67, 2, 16, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(68, 2, 17, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(69, 2, 18, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(70, 2, 19, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(71, 2, 20, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(72, 2, 21, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(73, 2, 22, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(74, 2, 23, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(75, 2, 24, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(76, 2, 25, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(77, 2, 26, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(78, 2, 27, 'readonly', '2018-08-06 02:21:05', '2018-08-06 02:21:05'),
(81, 2, 30, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(82, 2, 31, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(83, 2, 32, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(84, 2, 33, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(85, 2, 34, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(86, 2, 35, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(87, 2, 36, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(88, 2, 37, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(89, 2, 38, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(90, 2, 39, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(91, 2, 40, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(92, 2, 41, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(93, 2, 42, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(94, 2, 43, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(95, 2, 44, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(96, 2, 45, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(97, 2, 46, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(98, 2, 47, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(99, 2, 48, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(100, 2, 49, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(101, 2, 50, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(102, 2, 51, 'readonly', '2018-08-06 02:21:06', '2018-08-06 02:21:06'),
(103, 1, 52, 'write', '2018-08-06 02:34:36', '2018-08-06 02:34:36'),
(104, 1, 53, 'write', '2018-08-06 04:07:33', '2018-08-06 04:07:33'),
(105, 1, 54, 'write', '2018-08-06 04:09:20', '2018-08-06 04:09:20'),
(106, 1, 55, 'write', '2018-08-06 04:10:08', '2018-08-06 04:10:08'),
(107, 2, 52, 'invisible', '2018-08-06 04:12:35', '2018-08-06 04:12:35'),
(108, 2, 53, 'invisible', '2018-08-06 04:12:35', '2018-08-06 04:12:35'),
(109, 2, 54, 'invisible', '2018-08-06 04:12:35', '2018-08-06 04:12:35'),
(110, 2, 55, 'invisible', '2018-08-06 04:12:35', '2018-08-06 04:12:35'),
(111, 1, 56, 'write', '2018-08-06 04:20:33', '2018-08-06 04:20:33'),
(112, 1, 57, 'write', '2018-08-06 04:21:03', '2018-08-06 04:21:03'),
(113, 1, 58, 'write', '2018-08-06 04:21:21', '2018-08-06 04:21:21'),
(114, 1, 59, 'write', '2018-08-06 07:18:00', '2018-08-06 07:18:00'),
(115, 1, 60, 'write', '2018-08-06 07:18:24', '2018-08-06 07:18:24'),
(116, 1, 61, 'write', '2018-08-06 07:18:52', '2018-08-06 07:18:52'),
(117, 1, 62, 'write', '2018-08-06 07:32:12', '2018-08-06 07:32:12'),
(118, 1, 63, 'write', '2018-08-06 07:33:42', '2018-08-06 07:33:42'),
(119, 3, 1, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(120, 3, 2, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(121, 3, 3, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(122, 3, 4, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(123, 3, 5, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(124, 3, 6, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(125, 3, 7, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(126, 3, 8, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(127, 3, 9, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(128, 3, 10, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(129, 3, 11, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(130, 3, 12, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(134, 3, 16, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(135, 3, 17, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(136, 3, 18, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(137, 3, 19, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(138, 3, 20, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(139, 3, 21, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(140, 3, 26, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(141, 3, 23, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(142, 3, 24, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(143, 3, 25, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(144, 3, 59, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(145, 3, 60, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(146, 3, 22, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(147, 3, 61, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(149, 3, 27, 'readonly', '2018-08-08 01:00:06', '2018-08-08 01:00:06'),
(151, 3, 30, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(152, 3, 31, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(153, 3, 32, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(154, 3, 33, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(155, 3, 34, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(156, 3, 35, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(157, 3, 36, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(158, 3, 37, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(159, 3, 38, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(160, 3, 39, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(161, 3, 40, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(162, 3, 41, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(163, 3, 42, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(164, 3, 43, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(165, 3, 44, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(166, 3, 45, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(167, 3, 46, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(168, 3, 47, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(169, 3, 48, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(170, 3, 49, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(171, 3, 50, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(172, 3, 51, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(173, 3, 52, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(174, 3, 53, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(175, 3, 54, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(176, 3, 55, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(177, 3, 56, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(178, 3, 57, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(179, 3, 58, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(180, 3, 62, 'readonly', '2018-08-08 01:00:07', '2018-08-08 01:00:07'),
(181, 3, 63, 'readonly', '2018-08-08 01:00:08', '2018-08-08 01:00:08'),
(182, 1, 64, 'write', '2018-08-08 01:01:24', '2018-08-08 01:01:24'),
(183, 1, 65, 'write', '2018-08-08 01:01:46', '2018-08-08 01:01:46'),
(184, 1, 66, 'write', '2018-08-08 01:02:05', '2018-08-08 01:02:05'),
(185, 1, 67, 'write', '2018-08-08 01:02:44', '2018-08-08 01:02:44'),
(186, 1, 68, 'write', '2018-08-08 01:03:14', '2018-08-08 01:03:14'),
(187, 1, 69, 'write', '2018-08-08 01:04:05', '2018-08-08 01:04:05'),
(188, 1, 70, 'write', '2018-08-08 01:04:48', '2018-08-08 01:04:48'),
(189, 1, 71, 'write', '2018-08-08 01:05:09', '2018-08-08 01:05:09'),
(190, 1, 72, 'write', '2018-08-08 01:05:36', '2018-08-08 01:05:36'),
(191, 1, 73, 'write', '2018-08-08 01:06:08', '2018-08-08 01:06:08'),
(192, 1, 74, 'write', '2018-08-08 01:06:27', '2018-08-08 01:06:27'),
(193, 3, 64, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(194, 3, 65, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(195, 3, 66, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(196, 3, 67, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(197, 3, 68, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(198, 3, 69, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(199, 3, 70, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(200, 3, 71, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(201, 3, 72, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(202, 3, 73, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(203, 3, 74, 'write', '2018-08-08 01:26:45', '2018-08-08 01:26:45'),
(204, 1, 75, 'write', '2018-08-08 04:58:38', '2018-08-08 04:58:38'),
(205, 3, 75, 'write', '2018-08-10 01:42:31', '2018-08-10 01:42:31'),
(216, 1, 86, 'write', '2018-08-13 05:27:54', '2018-08-13 05:27:54'),
(217, 1, 87, 'write', '2018-08-13 05:28:23', '2018-08-13 05:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(3, 2, 2, NULL, NULL),
(6, 3, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sidebar_menu_accesses`
--

CREATE TABLE `sidebar_menu_accesses` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `menu_id` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sidebar_menu_accesses`
--

INSERT INTO `sidebar_menu_accesses` (`id`, `deleted_at`, `created_at`, `updated_at`, `role_id`, `menu_id`) VALUES
(1, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 1),
(2, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 5),
(3, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 14),
(4, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 17),
(5, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 19),
(6, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 20),
(7, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 21),
(8, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 22),
(9, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 23),
(10, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 24),
(11, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 25),
(12, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 26),
(13, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 27),
(14, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 28),
(15, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 29),
(16, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 30),
(17, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 31),
(18, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 32),
(19, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 33),
(24, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 40),
(25, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 41),
(26, NULL, '2018-08-13 11:03:31', '2018-08-13 11:03:31', 1, 42);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Dummy Task',
  `start_date` date NOT NULL DEFAULT '1970-01-01',
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `deleted_at`, `created_at`, `updated_at`, `name`, `start_date`, `end_date`) VALUES
(1, NULL, '2018-08-06 04:22:23', '2018-08-06 04:22:23', 'Create UI', '2018-08-06', '0000-00-00'),
(2, NULL, '2018-08-06 04:22:47', '2018-08-06 04:23:13', 'Plan Sprint', '2018-08-06', '1970-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

CREATE TABLE `timesheets` (
  `id` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `task_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `date` date NOT NULL DEFAULT '1970-01-01',
  `hours` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `minutes` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  `dependency` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `dependency_for` text COLLATE utf8_unicode_ci NOT NULL,
  `dependent_on` int(10) UNSIGNED DEFAULT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `manager_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `submitor_id` int(10) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `timesheets`
--

INSERT INTO `timesheets` (`id`, `deleted_at`, `created_at`, `updated_at`, `project_id`, `task_id`, `date`, `hours`, `minutes`, `comments`, `dependency`, `dependency_for`, `dependent_on`, `lead_id`, `manager_id`, `submitor_id`) VALUES
(1, NULL, '2018-08-08 04:27:41', '2018-08-08 04:27:41', 1, 1, '2018-08-08', '5', '30', 'This is just for testing', 'No', '', 1, 1, 1, 1),
(2, NULL, '2018-08-08 04:39:50', '2018-08-08 04:39:50', 1, 1, '2018-08-08', '6', '30', 'this is comment', 'No', '', 1, 1, 1, 1),
(3, NULL, '2018-08-08 05:01:33', '2018-08-08 05:01:33', 1, 1, '2018-08-08', '1', '00', '', 'no', '', 1, 1, 1, 1),
(6, NULL, '2018-08-09 01:18:42', '2018-08-09 01:18:42', 2, 1, '2018-08-09', '4', '30', 'Comment', 'No', '', 1, 1, 1, 1),
(8, NULL, '2018-08-09 04:38:58', '2018-08-09 04:38:58', 2, 2, '2018-08-09', '2', '00', 'sdad', 'No', '', NULL, 1, 1, 1),
(9, NULL, '2018-08-09 04:39:38', '2018-08-09 04:39:38', 2, 2, '2018-08-09', '2', '00', 'sdad', 'No', '', NULL, 1, 1, 1),
(10, NULL, '2018-08-09 04:40:25', '2018-08-09 04:40:25', 2, 2, '2018-08-09', '2', '00', 'sdad', 'No', '', NULL, 1, 1, 1),
(11, NULL, '2018-08-09 04:40:46', '2018-08-09 04:40:46', 2, 2, '2018-08-09', '2', '00', 'sdad', 'No', '', NULL, 1, 1, 1),
(12, NULL, '2018-08-09 04:41:32', '2018-08-09 04:41:32', 2, 2, '2018-08-09', '2', '00', 'sdad', 'No', '', NULL, 1, 1, 1),
(13, NULL, '2018-08-09 04:42:10', '2018-08-09 04:42:10', 1, 2, '2018-08-09', '4', '30', 'jh', 'No', '', NULL, 1, 1, 1),
(14, NULL, '2018-08-09 04:42:32', '2018-08-09 04:42:32', 2, 1, '2018-08-09', '6', '30', 'hhh', 'No', '', NULL, 1, 1, 1),
(15, NULL, '2018-08-09 04:44:15', '2018-08-09 04:44:15', 2, 1, '2018-08-09', '6', '30', 'hhh', 'No', '', NULL, 1, 1, 1),
(16, NULL, '2018-08-09 04:44:43', '2018-08-09 04:44:43', 2, 2, '2018-08-09', '1', '00', 'j', 'No', '', NULL, 1, 1, 1),
(17, NULL, '2018-08-09 04:45:54', '2018-08-09 04:45:54', 2, 2, '2018-08-09', '1', '00', 'j', 'No', '', 1, 1, 1, 1),
(18, NULL, '2018-08-09 04:46:06', '2018-08-09 04:46:06', 1, 2, '2018-08-09', '1', '00', 'j', 'No', '', 1, 1, 1, 1),
(19, NULL, '2018-08-09 04:47:44', '2018-08-09 04:47:44', 1, 2, '2018-08-09', '1', '00', 'j', 'No', '', 1, 1, 1, 1),
(20, NULL, '2018-08-09 04:48:27', '2018-08-09 04:48:27', 1, 2, '2018-08-09', '1', '00', 'j', 'No', '', 1, 1, 1, 1),
(21, NULL, '2018-08-09 04:51:48', '2018-08-09 04:51:48', 2, 2, '2018-08-01', '5', '30', 'hbub', 'No', '', NULL, 1, 1, 1),
(22, NULL, '2018-08-09 04:52:07', '2018-08-09 04:52:07', 1, 2, '2018-08-02', '5', '30', 'hbub', 'No', '', 1, 1, 1, 1),
(23, NULL, '2018-08-09 04:53:03', '2018-08-09 04:53:03', 1, 2, '2018-08-02', '5', '30', 'hbub', 'No', '', 1, 1, 1, 1),
(24, NULL, '2018-08-09 04:53:08', '2018-08-09 04:53:08', 1, 2, '2018-08-02', '5', '30', 'hbub', 'No', '', 1, 1, 1, 1),
(25, NULL, '2018-08-09 04:53:13', '2018-08-09 04:53:13', 2, 2, '2018-08-02', '5', '30', 'hbub', 'No', '', 1, 1, 1, 1),
(26, NULL, '2018-08-09 04:57:58', '2018-08-09 04:57:58', 1, 1, '2018-08-06', '7', '00', '', 'No', '', NULL, 1, 1, 1),
(27, NULL, '2018-08-09 05:03:57', '2018-08-09 05:03:57', 1, 1, '2018-08-22', '1', '00', '', 'No', '', NULL, 1, 1, 1),
(28, NULL, '2018-08-09 05:04:01', '2018-08-09 05:04:01', 1, 1, '2018-08-20', '1', '00', '', 'No', '', 1, 1, 1, 1),
(29, NULL, '2018-08-09 05:04:12', '2018-08-09 05:04:12', 2, 1, '2018-08-20', '4', '00', '', 'No', '', 1, 1, 1, 1),
(30, NULL, '2018-08-09 05:04:23', '2018-08-09 05:04:23', 2, 1, '2018-08-20', '4', '30', '', 'No', '', 1, 1, 1, 1),
(31, NULL, '2018-08-09 05:05:49', '2018-08-09 05:05:49', 2, 1, '2018-08-20', '5', '30', '', 'No', '', 1, 1, 1, 1),
(32, NULL, '2018-08-09 05:06:10', '2018-08-09 05:06:10', 2, 1, '2018-08-20', '5', '30', '', 'No', '', 1, 1, 1, 1),
(33, NULL, '2018-08-09 05:20:55', '2018-08-09 05:20:55', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', NULL, 1, 1, 1),
(34, NULL, '2018-08-09 05:21:16', '2018-08-09 05:21:16', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', NULL, 1, 1, 1),
(35, NULL, '2018-08-09 05:21:22', '2018-08-09 05:21:22', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(36, NULL, '2018-08-09 05:22:11', '2018-08-09 05:22:11', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(37, NULL, '2018-08-09 05:22:19', '2018-08-09 05:22:19', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(38, NULL, '2018-08-09 05:48:52', '2018-08-09 05:48:52', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(39, NULL, '2018-08-09 05:52:33', '2018-08-09 05:52:33', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(40, NULL, '2018-08-09 05:53:18', '2018-08-09 05:53:18', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(41, NULL, '2018-08-09 05:54:00', '2018-08-09 05:54:00', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(42, NULL, '2018-08-09 05:59:24', '2018-08-09 05:59:24', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(43, NULL, '2018-08-09 05:59:54', '2018-08-09 05:59:54', 1, 1, '2018-08-06', '5', '30', 'bg', 'No', '', 1, 1, 1, 1),
(44, NULL, '2018-08-09 06:55:27', '2018-08-09 06:55:27', 1, 2, '2018-08-01', '2', '00', '', 'No', '', NULL, 1, 1, 1),
(45, NULL, '2018-08-09 07:42:40', '2018-08-09 07:42:40', 1, 2, '2018-08-01', '2', '00', '', 'No', '', NULL, 1, 1, 1),
(46, NULL, '2018-08-09 08:23:24', '2018-08-09 08:23:24', 1, 2, '2018-08-01', '2', '00', '', 'No', '', NULL, 1, 1, 1),
(47, NULL, '2018-08-10 00:33:47', '2018-08-10 00:33:47', 1, 2, '2018-08-10', '4', '30', 'Today\'s sheet 1', 'No', '', NULL, 1, 1, 1),
(48, NULL, '2018-08-10 00:55:36', '2018-08-10 00:55:36', 2, 1, '2018-08-10', '4', '00', 'Today\'s sheet 2', 'No', '', NULL, 1, 1, 1),
(49, NULL, '2018-08-10 00:59:15', '2018-08-10 00:59:15', 2, 1, '2018-08-10', '4', '00', 'Today\'s sheet 2', 'No', '', NULL, 1, 1, 1),
(50, NULL, '2018-08-10 01:04:55', '2018-08-10 01:04:55', 2, 1, '2018-08-10', '4', '00', 'Today\'s sheet 2', 'No', '', NULL, 1, 1, 1),
(51, NULL, '2018-08-10 01:05:28', '2018-08-10 01:05:28', 2, 1, '2018-08-10', '4', '00', 'Today\'s sheet 2', 'No', '', NULL, 1, 1, 1),
(52, NULL, '2018-08-10 01:12:58', '2018-08-10 01:12:58', 2, 1, '2018-08-10', '4', '00', 'Today\'s sheet 2', 'No', '', NULL, 1, 1, 1),
(53, NULL, '2018-08-10 01:17:54', '2018-08-10 01:17:54', 2, 1, '2018-08-10', '2', '30', 'Comments', 'No', '', NULL, 1, 1, 1),
(54, NULL, '2018-08-10 01:18:45', '2018-08-10 01:18:45', 2, 1, '2018-08-10', '1', '00', 'redirect', 'No', '', NULL, 1, 1, 1),
(55, NULL, '2018-08-10 01:26:15', '2018-08-10 01:26:15', 1, 2, '2018-08-10', '2', '30', 'fgd', 'Yes', 'rtet', 2, 1, 1, 1),
(56, NULL, '2018-08-10 02:53:49', '2018-08-10 02:53:49', 2, 2, '2018-08-10', '2', '30', 'Hey', 'Yes', 'For further comm', 1, 1, 1, 1),
(57, NULL, '2018-08-10 04:36:21', '2018-08-10 04:36:21', 1, 1, '2018-08-10', '3', '30', 'sadsada', 'No', '', NULL, 1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `path` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `caption` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `hash` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `context_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Employee',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `context_id`, `email`, `password`, `type`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Varsha', 1, 'varsha.mittal@ganitsoftech.com', '$2y$10$XCoW2KZEkr9ySzAUOKeTmOIW7NONucBOVQ6B4Mzp4qgHg28ofaBWK', 'Employee', 'BomTRRENVTjPCBKv1LZ7mFCndx4UmNwHpRMPanhJW55qweLZE1LRoYf5DDxG', NULL, '2018-08-06 01:55:58', '2018-08-13 05:34:43'),
(2, 'Employee 2', 2, 'employee.2@ganitsoftech.com', '$2y$10$hCzarBBd.gQLcodlRGp3BeY0YqORJc0uP3Uo8.oUlPl28JR2rM2MG', 'Employee', 'yyIJ7yfG7hYP9oRNwRVUaNB5YtlHNoZx2k3trxaj7blJwmWdbbTekDOpWTGo', NULL, '2018-08-06 02:22:34', '2018-08-13 05:34:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `backups_name_unique` (`name`),
  ADD UNIQUE KEY `backups_file_name_unique` (`file_name`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_name_unique` (`name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_email_unique` (`email`),
  ADD KEY `employees_dept_foreign` (`dept`),
  ADD KEY `employees_first_approver_foreign` (`first_approver`),
  ADD KEY `employees_second_approver_foreign` (`second_approver`),
  ADD KEY `employees_project_id_foreign` (`project_id`);

--
-- Indexes for table `la_configs`
--
ALTER TABLE `la_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `la_menus`
--
ALTER TABLE `la_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `managers_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module_fields`
--
ALTER TABLE `module_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_fields_module_foreign` (`module`),
  ADD KEY `module_fields_field_type_foreign` (`field_type`);

--
-- Indexes for table `module_field_types`
--
ALTER TABLE `module_field_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organizations_name_unique` (`name`),
  ADD UNIQUE KEY `organizations_email_unique` (`email`),
  ADD KEY `organizations_assigned_to_foreign` (`assigned_to`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_client_id_foreign` (`client_id`);

--
-- Indexes for table `rolemenus`
--
ALTER TABLE `rolemenus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rolemenus_role_id_foreign` (`role_id`),
  ADD KEY `rolemenus_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD KEY `roles_parent_foreign` (`parent`),
  ADD KEY `roles_dept_foreign` (`dept`);

--
-- Indexes for table `role_menu`
--
ALTER TABLE `role_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_menu_menu_id_foreign` (`menu_id`) USING BTREE,
  ADD KEY `role_menu_role_id_foreign` (`role_id`) USING BTREE;

--
-- Indexes for table `role_module`
--
ALTER TABLE `role_module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_module_role_id_foreign` (`role_id`),
  ADD KEY `role_module_module_id_foreign` (`module_id`);

--
-- Indexes for table `role_module_fields`
--
ALTER TABLE `role_module_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_module_fields_role_id_foreign` (`role_id`),
  ADD KEY `role_module_fields_field_id_foreign` (`field_id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `sidebar_menu_accesses`
--
ALTER TABLE `sidebar_menu_accesses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sidebar_menu_accesses_role_id_foreign` (`role_id`),
  ADD KEY `sidebar_menu_accesses_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timesheets_project_id_foreign` (`project_id`),
  ADD KEY `timesheets_task_id_foreign` (`task_id`),
  ADD KEY `timesheets_manager_id_foreign` (`manager_id`),
  ADD KEY `timesheets_lead_id_foreign` (`lead_id`),
  ADD KEY `timesheets_dependent_on_foreign` (`dependent_on`),
  ADD KEY `timesheets_submitor_id_foreign` (`submitor_id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploads_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `la_configs`
--
ALTER TABLE `la_configs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `la_menus`
--
ALTER TABLE `la_menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `managers`
--
ALTER TABLE `managers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `module_fields`
--
ALTER TABLE `module_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `module_field_types`
--
ALTER TABLE `module_field_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rolemenus`
--
ALTER TABLE `rolemenus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_menu`
--
ALTER TABLE `role_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `role_module`
--
ALTER TABLE `role_module`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `role_module_fields`
--
ALTER TABLE `role_module_fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sidebar_menu_accesses`
--
ALTER TABLE `sidebar_menu_accesses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timesheets`
--
ALTER TABLE `timesheets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_dept_foreign` FOREIGN KEY (`dept`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `employees_first_approver_foreign` FOREIGN KEY (`first_approver`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `employees_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `employees_second_approver_foreign` FOREIGN KEY (`second_approver`) REFERENCES `employees` (`id`);

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `managers`
--
ALTER TABLE `managers`
  ADD CONSTRAINT `managers_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `module_fields`
--
ALTER TABLE `module_fields`
  ADD CONSTRAINT `module_fields_field_type_foreign` FOREIGN KEY (`field_type`) REFERENCES `module_field_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `module_fields_module_foreign` FOREIGN KEY (`module`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `organizations_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`);

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `organizations` (`id`);

--
-- Constraints for table `rolemenus`
--
ALTER TABLE `rolemenus`
  ADD CONSTRAINT `rolemenus_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `rolemenus_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_dept_foreign` FOREIGN KEY (`dept`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `roles_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `roles` (`id`);

--
-- Constraints for table `role_module`
--
ALTER TABLE `role_module`
  ADD CONSTRAINT `role_module_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_module_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_module_fields`
--
ALTER TABLE `role_module_fields`
  ADD CONSTRAINT `role_module_fields_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `module_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_module_fields_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sidebar_menu_accesses`
--
ALTER TABLE `sidebar_menu_accesses`
  ADD CONSTRAINT `sidebar_menu_accesses_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `la_menus` (`id`),
  ADD CONSTRAINT `sidebar_menu_accesses_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD CONSTRAINT `timesheets_dependent_on_foreign` FOREIGN KEY (`dependent_on`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `timesheets_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  ADD CONSTRAINT `timesheets_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `managers` (`id`),
  ADD CONSTRAINT `timesheets_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `timesheets_submitor_id_foreign` FOREIGN KEY (`submitor_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `timesheets_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;