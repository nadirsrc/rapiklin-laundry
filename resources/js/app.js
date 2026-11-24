document.addEventListener('DOMContentLoaded', () => {
    // Mobile sidebar drawer
    const sidebar = document.getElementById('mobile-sidebar');
    const backdrop = document.getElementById('mobile-sidebar-backdrop');
    const openBtn = document.getElementById('mobile-menu-open');
    const closeBtn = document.getElementById('mobile-menu-close');

    if (sidebar && backdrop && openBtn) {
        const setSidebarOpen = (isOpen) => {
            sidebar.classList.toggle('-translate-x-full', !isOpen);
            if (isOpen) {
                backdrop.classList.remove('hidden');
                requestAnimationFrame(() => backdrop.classList.remove('opacity-0'));
            } else {
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        backdrop.classList.add('hidden');
                    }
                }, 180);
            }
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        openBtn.addEventListener('click', () => setSidebarOpen(true));
        backdrop.addEventListener('click', () => setSidebarOpen(false));
        if (closeBtn) {
            closeBtn.addEventListener('click', () => setSidebarOpen(false));
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                setSidebarOpen(false);
            }
        });
    }

    // Unified custom dropdowns & date pickers (replaces OS-native popups cleanly)
    const closeAllDropdowns = (exceptEl = null) => {
        document.querySelectorAll('[data-custom-select], [data-customer-picker], [data-custom-date]').forEach((root) => {
            if (root === exceptEl) return;
            const menu = root.querySelector('[data-select-menu], [data-picker-menu], [data-date-menu]');
            const trigger = root.querySelector('[data-select-trigger], [data-picker-trigger], [data-date-trigger]');
            const chevron = root.querySelector('[data-select-chevron], [data-picker-chevron]');
            if (menu) menu.classList.add('hidden');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180');
        });
    };

    document.querySelectorAll('[data-custom-select]').forEach((root) => {
        const hiddenInput = root.querySelector('input[type="hidden"]');
        const trigger = root.querySelector('[data-select-trigger]');
        const labelEl = root.querySelector('[data-select-label]');
        const chevron = root.querySelector('[data-select-chevron]');
        const menu = root.querySelector('[data-select-menu]');
        const options = root.querySelectorAll('[data-select-option]');

        if (!hiddenInput || !trigger || !menu) return;

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            if (trigger.disabled) return;
            const isHidden = menu.classList.contains('hidden');
            closeAllDropdowns(isHidden ? root : null);
            menu.classList.toggle('hidden', !isHidden);
            trigger.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            if (chevron) chevron.classList.toggle('rotate-180', isHidden);
        });

        options.forEach((optBtn) => {
            optBtn.addEventListener('click', () => {
                const value = optBtn.dataset.value ?? '';
                const label = optBtn.dataset.label ?? '';
                hiddenInput.value = value;
                if (labelEl) labelEl.textContent = label;

                options.forEach((item) => {
                    const selected = item === optBtn;
                    item.setAttribute('aria-selected', selected ? 'true' : 'false');
                    item.classList.toggle('bg-brand-soft', selected);
                    item.classList.toggle('font-semibold', selected);
                    item.classList.toggle('text-brand', selected);
                    item.classList.toggle('text-ink', !selected);
                    item.classList.toggle('hover:bg-canvas', !selected);
                    const check = item.querySelector('[data-select-check]');
                    if (check) check.classList.toggle('opacity-0', !selected);
                });

                menu.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
                if (chevron) chevron.classList.remove('rotate-180');
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });

    // Searchable customer picker in Transaksi Baru
    document.querySelectorAll('[data-customer-picker]').forEach((picker) => {
        const trigger = picker.querySelector('[data-picker-trigger]');
        const menu = picker.querySelector('[data-picker-menu]');
        const chevron = picker.querySelector('[data-picker-chevron]');
        const labelEl = picker.querySelector('[data-picker-label]');
        const searchInput = picker.querySelector('[data-picker-search]');
        const emptyState = picker.querySelector('[data-picker-empty]');
        const clearBtn = picker.querySelector('[data-picker-clear]');
        const items = picker.querySelectorAll('[data-picker-item]');

        const namaInput = document.getElementById('nama_pelanggan');
        const hpInput = document.getElementById('no_hp');
        const alamatInput = document.getElementById('alamat');

        if (!trigger || !menu) return;

        const closePicker = () => {
            menu.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180');
        };

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            closeAllDropdowns(isHidden ? picker : null);
            menu.classList.toggle('hidden', !isHidden);
            trigger.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            if (chevron) chevron.classList.toggle('rotate-180', isHidden);
            if (isHidden && searchInput) {
                setTimeout(() => searchInput.focus(), 40);
            }
        });

        menu.addEventListener('click', (event) => event.stopPropagation());

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const q = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;
                items.forEach((item) => {
                    const haystack = (item.dataset.search || '').toLowerCase();
                    const matches = q === '' || haystack.includes(q);
                    item.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });
                if (emptyState) {
                    emptyState.classList.toggle('hidden', visibleCount > 0);
                }
            });
        }

        const markSelectedItem = (selectedBtn) => {
            items.forEach((item) => {
                const isSelected = item === selectedBtn;
                item.classList.toggle('bg-brand-soft', isSelected);
                item.classList.toggle('border-brand/30', isSelected);
                item.classList.toggle('border-transparent', !isSelected);
                const check = item.querySelector('[data-picker-check]');
                if (check) check.classList.toggle('opacity-0', !isSelected);
            });
        };

        items.forEach((item) => {
            item.addEventListener('click', () => {
                const nama = item.dataset.nama || '';
                const hp = item.dataset.hp || '';
                const alamat = item.dataset.alamat || '';

                if (namaInput) namaInput.value = nama;
                if (hpInput) hpInput.value = hp;
                if (alamatInput) alamatInput.value = alamat;

                if (labelEl) {
                    labelEl.textContent = nama + ' (' + hp + ')';
                    labelEl.classList.remove('text-ink-muted');
                    labelEl.classList.add('text-ink', 'font-semibold');
                }

                markSelectedItem(item);
                closePicker();

                [namaInput, hpInput].forEach((el) => {
                    if (!el) return;
                    el.classList.add('ring-2', 'ring-brand/20');
                    setTimeout(() => el.classList.remove('ring-2', 'ring-brand/20'), 280);
                });
            });
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (namaInput) namaInput.value = '';
                if (hpInput) hpInput.value = '';
                if (alamatInput) alamatInput.value = '';
                if (labelEl) {
                    labelEl.textContent = 'Pilih pelanggan langganan...';
                    labelEl.classList.add('text-ink-muted');
                    labelEl.classList.remove('text-ink', 'font-semibold');
                }
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
                markSelectedItem(null);
                closePicker();
                if (namaInput) namaInput.focus();
            });
        }
    });

    // Custom calendar date picker
    const monthsFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const parseYmd = (str) => {
        if (!str || !/^\d{4}-\d{2}-\d{2}$/.test(str)) return null;
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d);
    };

    const formatYmd = (date) => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    const formatDisplayDate = (date) => {
        const d = String(date.getDate()).padStart(2, '0');
        return `${d} ${monthsShort[date.getMonth()]} ${date.getFullYear()}`;
    };

    document.querySelectorAll('[data-custom-date]').forEach((root) => {
        const input = root.querySelector('[data-date-input]');
        const trigger = root.querySelector('[data-date-trigger]');
        const labelEl = root.querySelector('[data-date-label]');
        const menu = root.querySelector('[data-date-menu]');
        const headingEl = root.querySelector('[data-date-heading]');
        const prevBtn = root.querySelector('[data-date-prev]');
        const nextBtn = root.querySelector('[data-date-next]');
        const gridEl = root.querySelector('[data-date-grid]');
        const clearBtn = root.querySelector('[data-date-clear]');
        const todayBtn = root.querySelector('[data-date-today]');
        const monthToggle = root.querySelector('[data-date-month-toggle]');
        const monthPanel = root.querySelector('[data-date-month-panel]');
        const dayPanel = root.querySelector('[data-date-day-panel]');
        const monthGrid = root.querySelector('[data-month-grid]');
        const yearLabel = root.querySelector('[data-year-label]');
        const yearPrev = root.querySelector('[data-year-prev]');
        const yearNext = root.querySelector('[data-year-next]');

        if (!input || !trigger || !menu || !gridEl) return;

        const now = new Date();
        const todayYmd = formatYmd(now);
        const initial = parseYmd(input.value) || now;
        let viewYear = initial.getFullYear();
        let viewMonth = initial.getMonth();
        let selectingMonth = false;

        const setMonthMode = (open) => {
            selectingMonth = open;
            if (monthPanel) monthPanel.classList.toggle('hidden', !open);
            if (dayPanel) dayPanel.classList.toggle('hidden', open);
        };

        const selectDate = (dateObj) => {
            if (dateObj) {
                input.value = formatYmd(dateObj);
                viewYear = dateObj.getFullYear();
                viewMonth = dateObj.getMonth();
                if (labelEl) {
                    labelEl.textContent = formatDisplayDate(dateObj);
                    labelEl.classList.remove('text-ink-muted');
                    labelEl.classList.add('font-medium', 'text-ink');
                }
            } else {
                input.value = '';
                if (labelEl) {
                    labelEl.textContent = 'Pilih tanggal';
                    labelEl.classList.add('text-ink-muted');
                    labelEl.classList.remove('font-medium', 'text-ink');
                }
            }

            menu.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
            setMonthMode(false);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        const renderMonthGrid = () => {
            if (!monthGrid || !yearLabel) return;
            yearLabel.textContent = String(viewYear);
            monthGrid.innerHTML = '';

            monthsShort.forEach((shortName, mIdx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                const isCurrentView = mIdx === viewMonth;
                btn.textContent = shortName;
                btn.className = isCurrentView
                    ? 'rounded-xl bg-brand px-2 py-2 text-xs font-semibold text-white'
                    : 'rounded-xl px-2 py-2 text-xs font-medium text-ink transition hover:bg-canvas';
                btn.addEventListener('click', () => {
                    viewMonth = mIdx;
                    setMonthMode(false);
                    renderCalendar();
                });
                monthGrid.appendChild(btn);
            });
        };

        const renderCalendar = () => {
            if (headingEl) {
                headingEl.textContent = `${monthsFull[viewMonth]} ${viewYear}`;
            }
            if (yearLabel) {
                yearLabel.textContent = String(viewYear);
            }
            gridEl.innerHTML = '';

            const selectedYmd = input.value;
            const firstOfMonth = new Date(viewYear, viewMonth, 1);
            const startDay = firstOfMonth.getDay();
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
            const daysInPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

            for (let i = startDay - 1; i >= 0; i--) {
                const d = daysInPrevMonth - i;
                const cellDate = new Date(viewYear, viewMonth - 1, d);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = String(d);
                btn.className = 'flex h-8 w-full items-center justify-center rounded-xl text-ink-muted/40 transition hover:bg-canvas hover:text-ink-muted';
                btn.addEventListener('click', () => selectDate(cellDate));
                gridEl.appendChild(btn);
            }

            for (let d = 1; d <= daysInMonth; d++) {
                const cellDate = new Date(viewYear, viewMonth, d);
                const ymd = formatYmd(cellDate);
                const isSelected = ymd === selectedYmd;
                const isToday = ymd === todayYmd;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = String(d);

                if (isSelected) {
                    btn.className = 'flex h-8 w-full items-center justify-center rounded-xl bg-brand font-semibold text-white shadow-sm';
                } else if (isToday) {
                    btn.className = 'flex h-8 w-full items-center justify-center rounded-xl bg-brand-soft font-semibold text-brand ring-1 ring-brand/35 transition hover:bg-brand hover:text-white';
                } else {
                    btn.className = 'flex h-8 w-full items-center justify-center rounded-xl font-medium text-ink transition hover:bg-canvas';
                }

                btn.addEventListener('click', () => selectDate(cellDate));
                gridEl.appendChild(btn);
            }

            const totalCells = startDay + daysInMonth;
            const trailing = (7 - (totalCells % 7)) % 7;
            for (let d = 1; d <= trailing; d++) {
                const cellDate = new Date(viewYear, viewMonth + 1, d);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = String(d);
                btn.className = 'flex h-8 w-full items-center justify-center rounded-xl text-ink-muted/40 transition hover:bg-canvas hover:text-ink-muted';
                btn.addEventListener('click', () => selectDate(cellDate));
                gridEl.appendChild(btn);
            }
        };

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            closeAllDropdowns(isHidden ? root : null);
            if (isHidden) {
                const currentVal = parseYmd(input.value);
                if (currentVal) {
                    viewYear = currentVal.getFullYear();
                    viewMonth = currentVal.getMonth();
                }
                setMonthMode(false);
                renderCalendar();
            }
            menu.classList.toggle('hidden', !isHidden);
            trigger.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });

        menu.addEventListener('click', (event) => event.stopPropagation());

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                viewMonth -= 1;
                if (viewMonth < 0) {
                    viewMonth = 11;
                    viewYear -= 1;
                }
                renderCalendar();
                if (selectingMonth) renderMonthGrid();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                viewMonth += 1;
                if (viewMonth > 11) {
                    viewMonth = 0;
                    viewYear += 1;
                }
                renderCalendar();
                if (selectingMonth) renderMonthGrid();
            });
        }

        if (monthToggle) {
            monthToggle.addEventListener('click', () => {
                setMonthMode(!selectingMonth);
                if (selectingMonth) renderMonthGrid();
            });
        }

        if (yearPrev) {
            yearPrev.addEventListener('click', () => {
                viewYear -= 1;
                renderMonthGrid();
            });
        }

        if (yearNext) {
            yearNext.addEventListener('click', () => {
                viewYear += 1;
                renderMonthGrid();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', () => selectDate(null));
        }

        if (todayBtn) {
            todayBtn.addEventListener('click', () => selectDate(new Date()));
        }
    });

    document.addEventListener('click', () => closeAllDropdowns());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    // Tactile submit feedback & double-submit protection
    document.querySelectorAll('form[method="POST"], form[method="post"]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn || submitBtn.dataset.loadingBound === '1') {
                return;
            }
            submitBtn.dataset.loadingBound = '1';
            submitBtn.classList.add('opacity-80', 'cursor-wait');
        });
    });
});
