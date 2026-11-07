(() => {
  'use strict';

  const MAX_SOURCE_BYTES = 30 * 1024 * 1024;
  const MAX_CROPPED_BYTES = 340 * 1024;
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

  function element(tag, className, text = '') {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text) node.textContent = text;
    return node;
  }

  function cropGeometry(state) {
    const { naturalWidth: imageWidth, naturalHeight: imageHeight } = state.image;
    let width = imageWidth;
    let height = width / state.ratio;
    if (height > imageHeight) {
      height = imageHeight;
      width = height * state.ratio;
    }

    width /= state.zoom;
    height /= state.zoom;
    const maxX = Math.max(0, imageWidth - width);
    const maxY = Math.max(0, imageHeight - height);

    return {
      x: maxX * state.x / 100,
      y: maxY * state.y / 100,
      width,
      height,
      maxX,
      maxY,
    };
  }

  function drawCrop(state) {
    const canvas = state.canvas;
    const context = canvas.getContext('2d');
    const crop = cropGeometry(state);
    context.clearRect(0, 0, canvas.width, canvas.height);
    context.imageSmoothingEnabled = true;
    context.imageSmoothingQuality = 'high';
    context.drawImage(state.image, crop.x, crop.y, crop.width, crop.height, 0, 0, canvas.width, canvas.height);
    state.crop = crop;
  }

  function rangeField(labelText, min, max, value, step = 1) {
    const label = element('label', 'admin-crop-range');
    const text = element('span', '', labelText);
    const input = element('input');
    input.type = 'range';
    input.min = String(min);
    input.max = String(max);
    input.step = String(step);
    input.value = String(value);
    label.append(text, input);
    return { label, input };
  }

  function createEditor(input) {
    const ratio = Number.parseFloat(input.dataset.cropRatio || '1');
    if (!Number.isFinite(ratio) || ratio <= 0 || input.dataset.cropperReady === '1') return;
    input.dataset.cropperReady = '1';

    const editor = element('div', 'admin-crop-editor');
    editor.hidden = true;
    const heading = element('div', 'admin-crop-heading');
    heading.append(element('strong', '', 'Sesuaikan potongan gambar'));
    const instructions = element('p', 'admin-crop-help', 'Seret gambar di dalam bingkai atau geser pengatur posisi.');
    const viewport = element('div', 'admin-crop-viewport');
    viewport.style.aspectRatio = String(ratio);
    viewport.tabIndex = 0;
    viewport.setAttribute('role', 'img');
    viewport.setAttribute('aria-label', 'Pratinjau crop gambar. Seret untuk mengubah posisi.');
    const canvas = element('canvas', 'admin-crop-canvas');
    canvas.width = 768;
    canvas.height = Math.round(canvas.width / ratio);
    viewport.append(canvas);

    const controls = element('div', 'admin-crop-controls');
    const zoom = rangeField('Perbesaran', 100, 300, 100);
    const horizontal = rangeField('Posisi kiri / kanan', 0, 100, 50);
    const vertical = rangeField('Posisi atas / bawah', 0, 100, 50);
    const actions = element('div', 'admin-crop-actions');
    const reset = element('button', 'button button-small button-ghost', 'Atur ulang crop');
    reset.type = 'button';
    const status = element('p', 'admin-crop-status', 'Hasil dipotong dan dikompresi otomatis sebelum dikirim.');
    actions.append(reset);
    controls.append(zoom.label, horizontal.label, vertical.label, actions, status);
    editor.append(heading, instructions, viewport, controls);
    input.closest('label')?.after(editor);

    const state = {
      ratio,
      image: null,
      canvas,
      viewport,
      zoom: 1,
      x: 50,
      y: 50,
      crop: null,
      status,
      objectUrl: null,
      dragging: false,
      pointerX: 0,
      pointerY: 0,
    };
    input._portfolioCropState = state;

    const render = () => {
      if (! state.image) return;
      horizontal.input.value = String(state.x);
      vertical.input.value = String(state.y);
      drawCrop(state);
      horizontal.input.disabled = state.crop.maxX < 1;
      vertical.input.disabled = state.crop.maxY < 1;
    };

    zoom.input.addEventListener('input', () => {
      state.zoom = Number(zoom.input.value) / 100;
      render();
    });
    horizontal.input.addEventListener('input', () => {
      state.x = Number(horizontal.input.value);
      render();
    });
    vertical.input.addEventListener('input', () => {
      state.y = Number(vertical.input.value);
      render();
    });
    reset.addEventListener('click', () => {
      state.zoom = 1;
      state.x = 50;
      state.y = 50;
      zoom.input.value = '100';
      render();
    });

    viewport.addEventListener('pointerdown', (event) => {
      if (! state.image) return;
      state.dragging = true;
      state.pointerX = event.clientX;
      state.pointerY = event.clientY;
      viewport.setPointerCapture(event.pointerId);
    });
    viewport.addEventListener('pointermove', (event) => {
      if (! state.dragging || ! state.crop) return;
      const deltaX = event.clientX - state.pointerX;
      const deltaY = event.clientY - state.pointerY;
      state.pointerX = event.clientX;
      state.pointerY = event.clientY;
      if (state.crop.maxX > 0 && viewport.clientWidth > 0) {
        state.x = clamp(state.x - deltaX / viewport.clientWidth * state.crop.width / state.crop.maxX * 100, 0, 100);
      }
      if (state.crop.maxY > 0 && viewport.clientHeight > 0) {
        state.y = clamp(state.y - deltaY / viewport.clientHeight * state.crop.height / state.crop.maxY * 100, 0, 100);
      }
      render();
    });
    viewport.addEventListener('pointerup', () => { state.dragging = false; });
    viewport.addEventListener('pointercancel', () => { state.dragging = false; });

    input.addEventListener('change', () => {
      const file = input.files?.[0];
      if (state.objectUrl) URL.revokeObjectURL(state.objectUrl);
      state.image = null;
      editor.hidden = true;
      input.setCustomValidity('');
      if (! file) return;

      if (file.size > MAX_SOURCE_BYTES) {
        input.value = '';
        input.setCustomValidity('Pilih gambar sumber berukuran maksimal 30 MB.');
        input.reportValidity();
        return;
      }
      if (! ['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        input.value = '';
        input.setCustomValidity('Gunakan gambar JPG, PNG, atau WebP.');
        input.reportValidity();
        return;
      }

      state.objectUrl = URL.createObjectURL(file);
      const image = new Image();
      image.onload = () => {
        if (image.naturalWidth * image.naturalHeight > 40000000) {
          URL.revokeObjectURL(state.objectUrl);
          state.objectUrl = null;
          input.value = '';
          input.setCustomValidity('Resolusi gambar terlalu besar untuk diproses.');
          input.reportValidity();
          return;
        }
        state.image = image;
        state.zoom = 1;
        state.x = 50;
        state.y = 50;
        zoom.input.value = '100';
        status.textContent = `Gambar sumber: ${Math.round(file.size / 1024)} KB. Atur potongan yang ingin ditampilkan.`;
        editor.hidden = false;
        render();
      };
      image.onerror = () => {
        URL.revokeObjectURL(state.objectUrl);
        state.objectUrl = null;
        input.value = '';
        input.setCustomValidity('File tersebut tidak dapat dibuka sebagai gambar.');
        input.reportValidity();
      };
      image.src = state.objectUrl;
    });
  }

  async function canvasBlob(canvas, quality) {
    return new Promise((resolve, reject) => {
      canvas.toBlob((blob) => blob ? resolve(blob) : reject(new Error('Browser gagal menyiapkan gambar.')), 'image/webp', quality);
    });
  }

  async function prepareCrop(input) {
    const state = input._portfolioCropState;
    if (! state?.image) throw new Error('Pilih ulang gambar agar pratinjau crop dapat diproses.');

    const baseWidth = state.ratio === 1 ? 768 : 1280;
    let scale = 1;
    let quality = 0.86;
    for (let attempt = 0; attempt < 20; attempt += 1) {
      const width = Math.max(320, Math.round(baseWidth * scale));
      state.canvas.width = width;
      state.canvas.height = Math.max(180, Math.round(width / state.ratio));
      drawCrop(state);
      const blob = await canvasBlob(state.canvas, quality);
      if (blob.type !== 'image/webp') {
        throw new Error('Browser ini tidak mendukung optimasi WebP. Gunakan browser yang lebih baru.');
      }
      if (blob.size <= MAX_CROPPED_BYTES) return blob;

      if (quality > 0.56) {
        quality -= 0.1;
      } else {
        scale *= 0.84;
        quality = 0.82;
      }
      if (width <= 320 && quality <= 0.56) break;
    }

    throw new Error('Ukuran hasil crop belum cukup kecil. Kurangi perbesaran lalu coba lagi.');
  }

  document.querySelectorAll('input[type="file"][data-crop-ratio]').forEach(createEditor);

  document.querySelectorAll('form[enctype="multipart/form-data"]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      if (form.dataset.cropReady === '1') {
        delete form.dataset.cropReady;
        return;
      }
      if (form.dataset.cropBusy === '1') {
        event.preventDefault();
        return;
      }

      const uploads = [...form.querySelectorAll('input[type="file"][data-crop-ratio]')]
        .filter((input) => input.files?.length);
      if (! uploads.length) return;

      event.preventDefault();
      form.dataset.cropBusy = '1';
      form.setAttribute('aria-busy', 'true');
      let currentInput = null;
      try {
        for (const input of uploads) {
          currentInput = input;
          const state = input._portfolioCropState;
          const blob = await prepareCrop(input);
          const transfer = new DataTransfer();
          transfer.items.add(new File([blob], 'cropped.webp', { type: 'image/webp', lastModified: Date.now() }));
          input.files = transfer.files;
          state.status.textContent = `Hasil crop siap: ${Math.ceil(blob.size / 1024)} KB.`;
        }
        delete form.dataset.cropBusy;
        form.removeAttribute('aria-busy');
        form.dataset.cropReady = '1';
        if (event.submitter) form.requestSubmit(event.submitter);
        else form.requestSubmit();
      } catch (error) {
        delete form.dataset.cropBusy;
        form.removeAttribute('aria-busy');
        const message = error instanceof Error ? error.message : 'Gambar tidak berhasil diproses.';
        const state = currentInput?._portfolioCropState;
        if (state) state.status.textContent = message;
        else window.alert(message);
      }
    });
  });

  const copySearch = document.querySelector('#siteCopySearch');
  if (copySearch) {
    const clearButton = document.querySelector('#siteCopySearchClear');
    const searchStatus = document.querySelector('#siteCopySearchStatus');
    const groups = [...document.querySelectorAll('.admin-copy-group')];
    const fields = groups.flatMap((group) => [...group.querySelectorAll('.admin-copy-field')].map((field) => ({
      field,
      group,
      textarea: field.querySelector('textarea'),
      label: field.querySelector('.admin-copy-label > span')?.textContent || '',
    })));
    let originalOpenStates = null;

    const normalizeSearchText = (value) => value
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLocaleLowerCase('id');

    const updateCharacterCount = ({ field, textarea }) => {
      const count = field.querySelector('[data-copy-length]');
      if (count && textarea) count.textContent = `${[...textarea.value].length} / 2.000`;
    };

    const updateTextareaSize = ({ textarea }) => {
      if (!textarea || textarea.getClientRects().length === 0) return;
      textarea.style.height = 'auto';
      textarea.style.height = `${textarea.scrollHeight + 2}px`;
    };

    const updateCopySearch = () => {
      const query = normalizeSearchText(copySearch.value.trim());
      if (query && originalOpenStates === null) {
        originalOpenStates = groups.map((group) => group.open);
      }

      const terms = query.split(/\s+/).filter(Boolean);
      const visibleCounts = new Map(groups.map((group) => [group, 0]));
      let visibleTotal = 0;

      fields.forEach((entry) => {
        const haystack = normalizeSearchText([
          entry.label,
          entry.textarea?.name || '',
          entry.textarea?.value || '',
          entry.group.querySelector('summary strong')?.textContent || '',
        ].join(' '));
        const matches = terms.every((term) => haystack.includes(term));
        entry.field.hidden = Boolean(query) && !matches;
        if (matches) {
          visibleCounts.set(entry.group, visibleCounts.get(entry.group) + 1);
          visibleTotal += 1;
        }
      });

      groups.forEach((group, index) => {
        const count = visibleCounts.get(group);
        group.hidden = Boolean(query) && count === 0;
        const countLabel = group.querySelector('[data-copy-visible-count]');
        if (countLabel) countLabel.textContent = String(query ? count : group.querySelectorAll('.admin-copy-field').length);
        if (query && count > 0) group.open = true;
        else if (!query && originalOpenStates !== null) group.open = originalOpenStates[index];
      });

      if (searchStatus) {
        const total = fields.length;
        const visibleGroups = [...visibleCounts.values()].filter((count) => count > 0).length;
        searchStatus.textContent = query
          ? (visibleTotal > 0
            ? `Menampilkan ${visibleTotal} dari ${total} teks di ${visibleGroups} bagian.`
            : 'Tidak ada tulisan yang cocok. Coba kata lain.')
          : `${total} teks dalam ${groups.length} bagian.`;
      }
      if (clearButton) clearButton.hidden = !query;
      if (!query) originalOpenStates = null;
    };

    fields.forEach((entry) => {
      updateCharacterCount(entry);
      updateTextareaSize(entry);
      entry.textarea?.addEventListener('input', () => {
        updateCharacterCount(entry);
        updateTextareaSize(entry);
        if (copySearch.value.trim()) updateCopySearch();
      });
    });
    groups.forEach((group) => group.addEventListener('toggle', () => {
      if (group.open) fields.filter((entry) => entry.group === group).forEach(updateTextareaSize);
    }));
    window.addEventListener('resize', () => fields.forEach(updateTextareaSize));
    copySearch.addEventListener('input', updateCopySearch);
    clearButton?.addEventListener('click', () => {
      copySearch.value = '';
      updateCopySearch();
      copySearch.focus();
    });
    updateCopySearch();
  }
})();
