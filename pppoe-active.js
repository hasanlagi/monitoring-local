(function(root){
 'use strict';
 function summary(rows){const result={paid:0,free:0,blocked:0,unknown:0};for(const row of rows){const p=String(row.profile??'').trim();if(!p){result.unknown++;continue;}if(/^(blokir|isolir)$/i.test(p)){result.blocked++;continue;}if(/(?:^|[^a-z0-9])(free|gratis)(?=$|[^a-z0-9])/i.test(p))result.free++;else result.paid++;}return result;}
 if(typeof module==='object'&&module.exports)module.exports={summary};else root.HngPppoeActive=Object.freeze({summary});
})(typeof window==='object'?window:globalThis);
