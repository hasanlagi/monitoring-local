(() => {
  const section = document.createElement('section');
  section.id = 'sfpMonitoring';
  section.className = 'panel mb-3';
  section.innerHTML = `
    <div class="sfp-heading">
      <div><strong>Monitoring Redaman SFP</strong><span class="sfp-caption">RX/TX dBm · update 30 detik</span></div>
      <div class="sfp-controls"><span id="sfpSummary" role="status" class="sfp-caption">Memuat…</span><button id="sfpChoose" type="button" class="btn btn-sm btn-outline-info" aria-expanded="false">Pilih interface</button><button id="sfpSettings" type="button" class="btn btn-sm btn-outline-info" aria-expanded="false">Atur alarm</button></div>
    </div>
    <div id="sfpChoosePanel" hidden>
      <div class="sfp-caption">Centang kartu yang ingin ditampilkan. Pemantauan dan alarm tetap berjalan untuk semua interface.</div>
      <div id="sfpChoices" class="sfp-choice-list"></div>
      <button id="sfpShowAll" type="button" class="btn btn-sm btn-outline-info">Tampilkan semua</button><span id="sfpChoiceMessage" class="sfp-caption" role="status"></span>
    </div>
    <form id="sfpAlarmForm" hidden>
      <label>Interface <select id="sfpAlarmInterface" class="form-select form-select-sm" required></select></label>
      <label>Alarm jika RX ≤ <input id="sfpAlarmThreshold" class="form-control form-control-sm" type="number" min="-40" max="0" step="0.1" value="-20" required> dBm</label>
      <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
      <span id="sfpFormMessage" role="status" class="sfp-caption"></span>
    </form>
    <div id="sfpCards" class="sfp-grid"></div>`;
  const ping = document.getElementById('priorityPing');
  if (ping) ping.after(section);
  else document.getElementById('ping').before(section);

  const grid = document.getElementById('sfpCards');
  const form = document.getElementById('sfpAlarmForm');
  const select = document.getElementById('sfpAlarmInterface');
  const input = document.getElementById('sfpAlarmThreshold');
  const message = document.getElementById('sfpFormMessage');
  const settings = document.getElementById('sfpSettings');
  const summary = document.getElementById('sfpSummary');
  const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'}[c]));
  const metric = (value, unit) => value == null ? '—' : `${Number(value).toFixed(1)} ${unit}`;
  const time = value => value ? new Date(value * 1000).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'}) : '—';
  const cards = new Map();
  let data = [];
  let busy = false;
  let optionsKey = '';
  const storageKey = 'hngSfpVisibleInterfaces.v1';
  let selectedIds = null;
  let displayedRows = [];
  try {
    const saved = JSON.parse(localStorage.getItem(storageKey));
    if (Array.isArray(saved)) selectedIds = new Set(saved.map(String).filter(id => /^\d+$/.test(id)));
  } catch (error) { /* Default to all cards when storage is unavailable. */ }
  const chooser = document.getElementById('sfpChoose');
  const choosePanel = document.getElementById('sfpChoosePanel');
  const choices = document.getElementById('sfpChoices');
  const choiceMessage = document.getElementById('sfpChoiceMessage');

  function saveChoices() {
    try {
      localStorage.setItem(storageKey, JSON.stringify(selectedIds === null ? null : [...selectedIds]));
      choiceMessage.textContent = 'Pilihan tersimpan di browser ini.';
    } catch (error) { choiceMessage.textContent = 'Pilihan berlaku saat ini; browser tidak mengizinkan penyimpanan.'; }
    for (const checkbox of choices.querySelectorAll('input')) checkbox.checked = selectedIds === null || selectedIds.has(checkbox.value);
    render(displayedRows);
  }

  chooser.addEventListener('click', () => {
    choosePanel.hidden = !choosePanel.hidden;
    chooser.setAttribute('aria-expanded', String(!choosePanel.hidden));
  });
  choices.addEventListener('change', event => {
    const checkbox = event.target;
    if (!checkbox.matches('input[type="checkbox"]')) return;
    if (selectedIds === null) selectedIds = new Set(data.map(port => String(port.id)));
    if (checkbox.checked) selectedIds.add(checkbox.value);
    else selectedIds.delete(checkbox.value);
    saveChoices();
  });
  document.getElementById('sfpShowAll').addEventListener('click', () => { selectedIds = null; saveChoices(); });

  function fillThreshold() {
    const port = data.find(row => String(row.id) === select.value);
    if (port) input.value = port.threshold_rx;
  }

  function render(rows) {
    displayedRows = rows;
    const visible = rows.filter(port => selectedIds === null || selectedIds.has(String(port.id)));
    const active = new Set();
    visible.forEach(port => {
      const id = String(port.id);
      active.add(id);
      let card = cards.get(id);
      if (!card) { card = document.createElement('article'); grid.appendChild(card); cards.set(id, card); }
      const badData = ['stale', 'error'].includes(port.status.code);
      card.className = 'sfp-card' + (port.status.alarm ? ' sfp-alarm' : port.status.code === 'normal' ? ' sfp-normal' : ' sfp-neutral');
      const link = port.link === 'link-ok' ? 'UP' : port.link === 'no-link' ? 'DOWN' : '—';
      card.innerHTML = `<div class="sfp-card-head"><b title="${escape(port.interface_name)}">${escape(port.router_name)} · ${escape(port.interface_name)}</b><span class="sfp-state">${escape(port.status.label)}</span></div>
        <div class="sfp-values"><span>RX <strong>${metric(port.rx_dbm, 'dBm')}</strong></span><span>TX <strong>${metric(port.tx_dbm, 'dBm')}</strong></span><span>Suhu <b>${metric(port.temperature_c, '°C')}</b></span></div>
        <div class="sfp-foot"><span>Link ${link} · Alarm RX ≤ ${metric(port.threshold_rx, 'dBm')}</span><span>${badData ? 'Data terakhir ' : ''}${time(port.sampled_at)}</span></div>`;
    });
    for (const [id, card] of cards) { if (!active.has(id)) { card.remove(); cards.delete(id); } }
    if (!visible.length) grid.innerHTML = `<div class="sfp-empty">${rows.length ? 'Tidak ada kartu dipilih. Klik Pilih interface untuk menampilkan kartu.' : 'Menunggu pembacaan interface SFP dari MikroTik.'}</div>`;
    else grid.querySelector('.sfp-empty')?.remove();
    const alarmCount = rows.filter(port => port.status.alarm).length;
    const unavailable = rows.filter(port => ['error', 'stale'].includes(port.status.code)).length;
    const hiddenAlarms = rows.filter(port => port.status.alarm && !active.has(String(port.id))).length;
    summary.textContent = `${visible.length}/${rows.length} interface` + (alarmCount ? ` · ${alarmCount} alarm${hiddenAlarms ? ` (${hiddenAlarms} di kartu tersembunyi)` : ''}` : unavailable ? ` · ${unavailable} data belum tersedia` : '');
    summary.className = 'sfp-caption' + (alarmCount || unavailable ? ' text-warning' : '');
  }

  async function request(options) {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 8000);
    try {
      const response = await fetch('actions/sfp.php', {...options, signal:controller.signal, cache:'no-store'});
      const json = await response.json();
      if (!response.ok || !json.ok) throw new Error(json.error || 'Gagal membaca data SFP');
      return json;
    } finally { clearTimeout(timeout); }
  }

  async function load() {
    if (busy) return;
    busy = true;
    try {
      const json = await request();
      data = json.data;
      render(data);
      settings.disabled = !data.length;
      const key = JSON.stringify(data.map(port => [port.id, port.router_name, port.interface_name]));
      if (key !== optionsKey) {
        optionsKey = key;
        const previous = select.value;
        select.innerHTML = data.map(port => `<option value="${port.id}">${escape(port.router_name)} · ${escape(port.interface_name)}</option>`).join('');
        choices.innerHTML = data.map(port => `<label><input type="checkbox" value="${port.id}" ${selectedIds === null || selectedIds.has(String(port.id)) ? 'checked' : ''}> ${escape(port.router_name)} · ${escape(port.interface_name)}</label>`).join('');
        if (data.some(port => String(port.id) === previous)) select.value = previous;
        if (form.hidden) fillThreshold();
      }
    } catch (error) {
      render(data.map(port => ({...port, rx_dbm:null, tx_dbm:null, temperature_c:null, link:'unknown', status:{code:'error', label:'Data belum tersedia', alarm:false}})));
      summary.textContent = 'Gagal memperbarui SFP';
      summary.className = 'sfp-caption text-warning';
    } finally { busy = false; }
  }

  settings.addEventListener('click', () => {
    form.hidden = !form.hidden;
    settings.setAttribute('aria-expanded', String(!form.hidden));
    if (!form.hidden) { fillThreshold(); message.textContent = ''; }
  });
  select.addEventListener('change', () => { fillThreshold(); message.textContent = ''; });
  form.addEventListener('submit', async event => {
    event.preventDefault();
    const button = form.querySelector('button');
    button.disabled = true;
    try {
      const body = new FormData();
      body.append('id', select.value);
      body.append('threshold_rx', input.value);
      await request({method:'POST', body});
      message.textContent = 'Batas alarm tersimpan.';
      await load();
    } catch (error) { message.textContent = error.message; }
    finally { button.disabled = false; }
  });
  load();
  setInterval(load, 10000);
})();
