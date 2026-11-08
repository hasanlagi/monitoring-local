(function (root) {
  function pppoeAlarmState(count, reliable, previousAlarm) {
    const valid = Number.isInteger(count) && count >= 0;
    const known = reliable === true && valid;
    return {alarm:known ? count >= 15 : (valid && count >= 15) || Boolean(previousAlarm), reliable:known};
  }
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = {pppoeAlarmState};
    return;
  }
  const countNode = document.getElementById('kOff');
  const card = countNode?.closest('.stat');
  const meta = card?.querySelector('.stat-meta');
  const panel = document.querySelector('.inactive-panel');
  const banner = document.createElement('div');
  banner.className = 'pppoe-off-banner';
  banner.setAttribute('role', 'alert');
  banner.hidden = true;
  if (panel) panel.querySelector('.section-head')?.after(banner);
  let alarm = false;
  let lastCount = null;
  let lastUpdate = Date.now();

  root.updatePppoeOffAlarm = function (count, reliable) {
    lastUpdate = Date.now();
    const next = pppoeAlarmState(count, reliable, alarm);
    if (next.reliable || Number.isInteger(count) && count >= 15) lastCount = count;
    alarm = next.alarm;
    card?.classList.toggle('pppoe-off-alarm', alarm);
    panel?.classList.toggle('pppoe-off-alarm', alarm);
    card?.classList.toggle('pppoe-off-unknown', !next.reliable && !alarm);
    if (meta) meta.textContent = alarm ? (next.reliable ? 'ALARM · 15+ nonaktif' : 'ALARM · data belum lengkap') : next.reliable ? 'akun tidak aktif' : 'Data belum lengkap';
    if (!next.reliable && countNode) countNode.textContent = lastCount === null ? '—' : lastCount.toLocaleString('id-ID')+'*';
    banner.hidden = !alarm;
    if (alarm) {
      const text = next.reliable
        ? `ALARM: ${lastCount} PPPoE nonaktif. Batas alarm 15 pelanggan.`
        : `ALARM PPPoE masih aktif. Data belum lengkap; terakhir terpantau ${lastCount ?? '—'} nonaktif.`;
      if (banner.textContent !== text) banner.textContent = text;
    }
  };
  setInterval(() => { if (Date.now()-lastUpdate > 15000) root.updatePppoeOffAlarm(NaN, false); }, 5000);
})(typeof window === 'undefined' ? globalThis : window);
