import './bootstrap';
import { initAdminTurbo } from './admin/turbo';

if (document.body?.classList.contains('admin-shell')) {
    initAdminTurbo();
}
