<?php
/**
 * Biblioteca local de ícones SVG do NexStock.
 * Os símbolos são definidos uma vez por página e reutilizados por <use>.
 */
function nexstock_icons(): string
{
    return <<<'SVG'
<svg aria-hidden="true" width="0" height="0" style="position:absolute;overflow:hidden">
  <defs>
    <symbol id="icon-package" viewBox="0 0 24 24">
      <path d="M3 7.5 12 3l9 4.5-9 4.5-9-4.5Z"/><path d="m3 7.5 9 4.5v9l-9-4.5v-9Z"/><path d="m21 7.5-9 4.5v9l9-4.5v-9Z"/>
    </symbol>
    <symbol id="icon-home" viewBox="0 0 24 24">
      <path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h5v-6h4v6h5V10"/>
    </symbol>
    <symbol id="icon-products" viewBox="0 0 24 24">
      <rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h6M9 11h6M9 15h4"/>
    </symbol>
    <symbol id="icon-category" viewBox="0 0 24 24">
      <path d="M20 13 12 21l-9-9V4h8l9 9Z"/><circle cx="7.5" cy="7.5" r="1.5"/>
    </symbol>
    <symbol id="icon-movement" viewBox="0 0 24 24">
      <path d="M20 7h-9a5 5 0 0 0-5 5v1"/><path d="m17 4 3 3-3 3"/><path d="M4 17h9a5 5 0 0 0 5-5v-1"/><path d="m7 20-3-3 3-3"/>
    </symbol>
    <symbol id="icon-history" viewBox="0 0 24 24">
      <path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>
    </symbol>
    <symbol id="icon-users" viewBox="0 0 24 24">
      <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
      <circle cx="10" cy="7" r="4"/>
      <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
    </symbol>
    <symbol id="icon-logout" viewBox="0 0 24 24">
      <path d="M10 4H5v16h5"/><path d="M13 8l4 4-4 4"/><path d="M8 12h9"/>
    </symbol>
    <symbol id="icon-menu" viewBox="0 0 24 24">
      <path d="M4 6h16M4 12h16M4 18h16"/>
    </symbol>
    <symbol id="icon-plus" viewBox="0 0 24 24">
      <path d="M12 5v14M5 12h14"/>
    </symbol>
    <symbol id="icon-search" viewBox="0 0 24 24">
      <circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/>
    </symbol>
    <symbol id="icon-filter" viewBox="0 0 24 24">
      <path d="M3 5h18l-7 8v5l-4 2v-7L3 5Z"/>
    </symbol>
    <symbol id="icon-check" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16.5 9"/>
    </symbol>
    <symbol id="icon-x-circle" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>
    </symbol>
    <symbol id="icon-alert" viewBox="0 0 24 24">
      <path d="M10.3 4.3 2.8 17.5A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.5L13.7 4.3a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/>
    </symbol>
    <symbol id="icon-info" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/>
    </symbol>
    <symbol id="icon-eye" viewBox="0 0 24 24">
      <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/>
    </symbol>
    <symbol id="icon-eye-off" viewBox="0 0 24 24">
      <path d="m3 3 18 18M10.6 6.1A9.6 9.6 0 0 1 12 6c6 0 9.5 6 9.5 6a15 15 0 0 1-2.2 3M6.2 6.2C3.8 7.8 2.5 12 2.5 12s3.5 6 9.5 6c1.2 0 2.3-.2 3.3-.6"/><path d="M9.8 9.8a3 3 0 0 0 4.4 4.4"/>
    </symbol>
    <symbol id="icon-edit" viewBox="0 0 24 24">
      <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/>
    </symbol>
    <symbol id="icon-trash" viewBox="0 0 24 24">
      <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/>
    </symbol>
    <symbol id="icon-save" viewBox="0 0 24 24">
      <path d="M5 3h12l2 2v16H5V3Z"/><path d="M8 3v6h8V3M8 21v-7h8v7"/>
    </symbol>
    <symbol id="icon-chart" viewBox="0 0 24 24">
      <path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>
    </symbol>
    <symbol id="icon-inbox" viewBox="0 0 24 24">
      <path d="M4 4h16v16H4V4Z"/><path d="M4 14h4l2 3h4l2-3h4"/>
    </symbol>
    <symbol id="icon-bolt" viewBox="0 0 24 24">
      <path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>
    </symbol>
    <symbol id="icon-bulb" viewBox="0 0 24 24">
      <path d="M9 18h6M10 22h4"/><path d="M8 14c-1.2-1.1-2-2.7-2-4.5A6 6 0 0 1 18 9.5c0 1.8-.8 3.4-2 4.5-.8.8-1 1.5-1 2H9c0-.5-.2-1.2-1-2Z"/>
    </symbol>
    <symbol id="icon-close" viewBox="0 0 24 24">
      <path d="m6 6 12 12M18 6 6 18"/>
    </symbol>
    <symbol id="icon-arrow-up" viewBox="0 0 24 24">
      <path d="M12 20V4M5 11l7-7 7 7"/>
    </symbol>
    <symbol id="icon-arrow-down" viewBox="0 0 24 24">
      <path d="M12 4v16M5 13l7 7 7-7"/>
    </symbol>
  </defs>
</svg>
SVG;
}

function nexstock_icon(string $name, string $class = 'icon'): string
{
    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true" focusable="false"><use href="#icon-' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"></use></svg>';
}
