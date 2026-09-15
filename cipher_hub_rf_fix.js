// ════════════════════════════════════════
// PASTE THIS INTO cipher_hub.php
// Replace the entire Rail Fence JS section
// ════════════════════════════════════════

// RF algorithm functions (named _algo to avoid clash with UI handlers)
function rfEncryptAlgo(text, rails){
  if(rails<2) return text;
  const fence=Array.from({length:rails},()=>[]);
  let r=0,d=1;
  for(let i=0;i<text.length;i++){
    fence[r].push(text[i]);
    if(r===0)d=1; else if(r===rails-1)d=-1;
    r+=d;
  }
  return fence.map(row=>row.join('')).join('');
}
function rfDecryptAlgo(cipher, rails){
  if(rails<2) return cipher;
  const n=cipher.length;
  const pat=new Array(n);
  let r=0,d=1;
  for(let i=0;i<n;i++){
    pat[i]=r;
    if(r===0)d=1; else if(r===rails-1)d=-1;
    r+=d;
  }
  const lens=new Array(rails).fill(0);
  for(let i=0;i<n;i++) lens[pat[i]]++;
  const rows=[]; let idx=0;
  for(let row=0;row<rails;row++){ rows.push(cipher.slice(idx,idx+lens[row]).split('')); idx+=lens[row]; }
  const ptrs=new Array(rails).fill(0);
  let res='';
  for(let i=0;i<n;i++){ const rr=pat[i]; res+=rows[rr][ptrs[rr]++]; }
  return res;
}

// Zigzag diagram — shows the RF pattern visually
// diagId = 'rf-enc-diagram' or 'rf-dec-diagram'
// label  = the text being shown (encrypt input or decrypt result)
function showRFDiagram(text, rails, result, diagId){
  const diagEl=document.getElementById(diagId);
  if(!diagEl) return;
  const cap=Math.min(text.length,20);
  const sample=text.slice(0,cap).toUpperCase();
  const grid=Array.from({length:rails},()=>Array(cap).fill(''));
  let r=0,d=1;
  for(let i=0;i<cap;i++){
    grid[r][i]=sample[i];
    if(r===0)d=1; else if(r===rails-1)d=-1;
    r+=d;
  }
  let html='<div style="font-family:Courier Prime,monospace;font-size:.78rem;">';
  html+='<div style="color:var(--goldl);font-size:.7rem;margin-bottom:5px;letter-spacing:.06em;text-transform:uppercase;">Zigzag pattern (first '+cap+' chars):</div>';
  const railColors=['#f0cc70','#8ecae6','#a8e6cf','#ffb3ba','#bae1ff','#ffffba'];
  for(let row=0;row<rails;row++){
    html+='<div style="letter-spacing:4px;line-height:1.8;">';
    for(let col=0;col<cap;col++){
      if(grid[row][col]){
        html+='<span style="color:'+railColors[row%railColors.length]+';font-weight:700;">'+grid[row][col]+'</span>';
      } else {
        html+='<span style="color:rgba(184,168,138,.18);">·</span>';
      }
    }
    html+='</div>';
  }
  html+='<div style="margin-top:8px;font-size:.7rem;">';
  html+='<span style="color:var(--teal3);">Result: </span>';
  html+='<span style="color:var(--goldl);font-weight:700;">'+result+'</span>';
  html+='</div></div>';
  diagEl.innerHTML=html;
  diagEl.style.display='block';
}

// UI button handlers — called by onclick in HTML buttons
function rfEncrypt(){
  hideErr('rf-enc-err');
  const text=document.getElementById('rf-enc-input').value.trim().replace(/ /g,'');
  const rails=parseInt(document.getElementById('rf-enc-key').value)||3;
  if(!text){ showErr('rf-enc-err','Please enter a message.'); return; }
  if(rails>=text.length){
    showErr('rf-enc-err','Key ('+rails+') must be less than message length ('+text.length+' chars). Try key ≤ '+Math.max(2,Math.floor(text.length/2))+'.');
    return;
  }
  const result=rfEncryptAlgo(text,rails);
  document.getElementById('rf-enc-out').textContent=result;
  document.getElementById('rf-enc-result').classList.add('show');
  showRFDiagram(text,rails,result,'rf-enc-diagram');
  saveLog('cipher_save.php',{action:'encrypt',rails,input_text:text,output_text:result,source:'typed'});
}

function rfDecrypt(){
  hideErr('rf-dec-err');
  const text=document.getElementById('rf-dec-input').value.trim().replace(/ /g,'');
  const rails=parseInt(document.getElementById('rf-dec-key').value)||3;
  if(!text){ showErr('rf-dec-err','Please enter a message.'); return; }
  const result=rfDecryptAlgo(text,rails);
  document.getElementById('rf-dec-out').textContent=result;
  document.getElementById('rf-dec-result').classList.add('show');
  // Show zigzag diagram for the ORIGINAL text (cipher text) being decoded
  showRFDiagram(text,rails,result,'rf-dec-diagram');
  saveLog('cipher_save.php',{action:'decrypt',rails,input_text:text,output_text:result,source:'typed'});
}
