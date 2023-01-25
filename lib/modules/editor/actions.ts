import type { ChainedCommands, EditorT } from '../tiptap'

const icons = {
  bold: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M8 11h4.5a2.5 2.5 0 1 0 0-5H8v5zm10 4.5a4.5 4.5 0 0 1-4.5 4.5H6V4h6.5a4.5 4.5 0 0 1 3.256 7.606A4.498 4.498 0 0 1 18 15.5zM8 13v5h5.5a2.5 2.5 0 1 0 0-5H8z"/></g></svg>',
  italic: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M15 20H7v-2h2.927l2.116-12H9V4h8v2h-2.927l-2.116 12H15z"/></g></svg>',
  strike: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M17.154 14c.23.516.346 1.09.346 1.72 0 1.342-.524 2.392-1.571 3.147C14.88 19.622 13.433 20 11.586 20c-1.64 0-3.263-.381-4.87-1.144V16.6c1.52.877 3.075 1.316 4.666 1.316 2.551 0 3.83-.732 3.839-2.197a2.21 2.21 0 0 0-.648-1.603l-.12-.117H3v-2h18v2h-3.846zm-4.078-3H7.629a4.086 4.086 0 0 1-.481-.522C6.716 9.92 6.5 9.246 6.5 8.452c0-1.236.466-2.287 1.397-3.153C8.83 4.433 10.271 4 12.222 4c1.471 0 2.879.328 4.222.984v2.152c-1.2-.687-2.515-1.03-3.946-1.03-2.48 0-3.719.782-3.719 2.346 0 .42.218.786.654 1.099.436.313.974.562 1.613.75.62.18 1.297.414 2.03.699z"/></g></svg>',
  code: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M16.95 8.464l1.414-1.414 4.95 4.95-4.95 4.95-1.414-1.414L20.485 12 16.95 8.464zm-9.9 0L3.515 12l3.535 3.536-1.414 1.414L.686 12l4.95-4.95L7.05 8.464z"/></g></svg>',
  highlight: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M15.243 4.515l-6.738 6.737-.707 2.121-1.04 1.041 2.828 2.829 1.04-1.041 2.122-.707 6.737-6.738-4.242-4.242zm6.364 3.535a1 1 0 0 1 0 1.414l-7.779 7.779-2.12.707-1.415 1.414a1 1 0 0 1-1.414 0l-4.243-4.243a1 1 0 0 1 0-1.414l1.414-1.414.707-2.121 7.779-7.779a1 1 0 0 1 1.414 0l5.657 5.657zm-6.364-.707l1.414 1.414-4.95 4.95-1.414-1.414 4.95-4.95zM4.283 16.89l2.828 2.829-1.414 1.414-4.243-1.414 2.828-2.829z"/></g></svg>',
  link: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M18.364 15.536L16.95 14.12l1.414-1.414a5 5 0 1 0-7.071-7.071L9.879 7.05 8.464 5.636 9.88 4.222a7 7 0 0 1 9.9 9.9l-1.415 1.414zm-2.828 2.828l-1.415 1.414a7 7 0 0 1-9.9-9.9l1.415-1.414L7.05 9.88l-1.414 1.414a5 5 0 1 0 7.071 7.071l1.414-1.414 1.415 1.414zm-.708-10.607l1.415 1.415-7.071 7.07-1.415-1.414 7.071-7.07z"/></g></svg>',
  subscript: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M11 6v13H9V6H3V4h14v2h-6zm8.55 10.58a.8.8 0 1 0-1.32-.36l-1.154.33A2.001 2.001 0 0 1 19 14a2 2 0 0 1 1.373 3.454L18.744 19H21v1h-4v-1l2.55-2.42z"/></g></svg>',
  superscript: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M11 7v13H9V7H3V5h12v2h-4zm8.55-.42a.8.8 0 1 0-1.32-.36l-1.154.33A2.001 2.001 0 0 1 19 4a2 2 0 0 1 1.373 3.454L18.744 9H21v1h-4V9l2.55-2.42z"/></g></svg>',
  textstyle: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M15.246 14H8.754l-1.6 4H5l6-15h2l6 15h-2.154l-1.6-4zm-.8-2L12 5.885 9.554 12h4.892zM3 20h18v2H3v-2z"/></g></svg>',
  underline: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M8 3v9a4 4 0 1 0 8 0V3h2v9a6 6 0 1 1-12 0V3h2zM4 20h16v2H4v-2z"/></g></svg>',
  blockquote: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 0 1-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 0 1-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></g></svg>',
  bulletList: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M8 4h13v2H8V4zm-5-.5h3v3H3v-3zm0 7h3v3H3v-3zm0 7h3v3H3v-3zM8 11h13v2H8v-2zm0 7h13v2H8v-2z"/></g></svg>',
  codeBlock: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M3 3h18a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm1 2v14h16V5H4zm16 7l-3.536 3.536-1.414-1.415L17.172 12 15.05 9.879l1.414-1.415L20 12zM6.828 12l2.122 2.121-1.414 1.415L4 12l3.536-3.536L8.95 9.88 6.828 12zm4.416 5H9.116l3.64-10h2.128l-3.64 10z"fill-rule=nonzero /></g></svg>',
  document: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M14.828 7.757l-5.656 5.657a1 1 0 1 0 1.414 1.414l5.657-5.656A3 3 0 1 0 12 4.929l-5.657 5.657a5 5 0 1 0 7.071 7.07L19.071 12l1.414 1.414-5.657 5.657a7 7 0 1 1-9.9-9.9l5.658-5.656a5 5 0 0 1 7.07 7.07L12 16.244A3 3 0 1 1 7.757 12l5.657-5.657 1.414 1.414z"/></g></svg>',
  emoji: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2zm0 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 7c2 0 3.667.333 5 1a5 5 0 0 1-10 0c1.333-.667 3-1 5-1zM8.5 7a2.5 2.5 0 0 1 2.45 2h-4.9A2.5 2.5 0 0 1 8.5 7zm7 0a2.5 2.5 0 0 1 2.45 2h-4.9a2.5 2.5 0 0 1 2.45-2z"fill-rule=nonzero /></g></svg>',
  hardBreak: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M15 18h1.5a2.5 2.5 0 1 0 0-5H3v-2h13.5a4.5 4.5 0 1 1 0 9H15v2l-4-3 4-3v2zM3 4h18v2H3V4zm6 14v2H3v-2h6z"/></g></svg>',
  hashtag: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M7.784 14l.42-4H4V8h4.415l.525-5h2.011l-.525 5h3.989l.525-5h2.011l-.525 5H20v2h-3.784l-.42 4H20v2h-4.415l-.525 5h-2.011l.525-5H9.585l-.525 5H7.049l.525-5H4v-2h3.784zm2.011 0h3.99l.42-4h-3.99l-.42 4z"/></g></svg>',
  heading: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M17 11V4h2v17h-2v-8H7v8H5V4h2v7z"/></g></svg>',
  h1: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M13 20h-2v-7H4v7H2V4h2v7h7V4h2v16zm8-12v12h-2v-9.796l-2 .536V8.67L19.5 8H21z"/></g></svg>',
  h2: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M4 4v7h7V4h2v16h-2v-7H4v7H2V4h2zm14.5 4c2.071 0 3.75 1.679 3.75 3.75 0 .857-.288 1.648-.772 2.28l-.148.18L18.034 18H22v2h-7v-1.556l4.82-5.546c.268-.307.43-.709.43-1.148 0-.966-.784-1.75-1.75-1.75-.918 0-1.671.707-1.744 1.606l-.006.144h-2C14.75 9.679 16.429 8 18.5 8z"/></g></svg>',
  h3: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M22 8l-.002 2-2.505 2.883c1.59.435 2.757 1.89 2.757 3.617 0 2.071-1.679 3.75-3.75 3.75-1.826 0-3.347-1.305-3.682-3.033l1.964-.382c.156.806.866 1.415 1.718 1.415.966 0 1.75-.784 1.75-1.75s-.784-1.75-1.75-1.75c-.286 0-.556.069-.794.19l-1.307-1.547L19.35 10H15V8h7zM4 4v7h7V4h2v16h-2v-7H4v7H2V4h2z"/></g></svg>',
  h4: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M13 20h-2v-7H4v7H2V4h2v7h7V4h2v16zm9-12v8h1.5v2H22v2h-2v-2h-5.5v-1.34l5-8.66H22zm-2 3.133L17.19 16H20v-4.867z"/></g></svg>',
  h5: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M22 8v2h-4.323l-.464 2.636c.33-.089.678-.136 1.037-.136 2.21 0 4 1.79 4 4s-1.79 4-4 4c-1.827 0-3.367-1.224-3.846-2.897l1.923-.551c.24.836 1.01 1.448 1.923 1.448 1.105 0 2-.895 2-2s-.895-2-2-2c-.63 0-1.193.292-1.56.748l-1.81-.904L16 8h6zM4 4v7h7V4h2v16h-2v-7H4v7H2V4h2z"/></g></svg>',
  h6: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M21.097 8l-2.598 4.5c2.21 0 4.001 1.79 4.001 4s-1.79 4-4 4-4-1.79-4-4c0-.736.199-1.426.546-2.019L18.788 8h2.309zM4 4v7h7V4h2v16h-2v-7H4v7H2V4h2zm14.5 10.5c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2z"/></g></svg>',
  horizontalRule: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M2 11h2v2H2v-2zm4 0h12v2H6v-2zm14 0h2v2h-2v-2z"/></g></svg>',
  image: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M4.828 21l-.02.02-.021-.02H2.992A.993.993 0 0 1 2 20.007V3.993A1 1 0 0 1 2.992 3h18.016c.548 0 .992.445.992.993v16.014a1 1 0 0 1-.992.993H4.828zM20 15V5H4v14L14 9l6 6zm0 2.828l-6-6L6.828 19H20v-1.172zM8 11a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></g></svg>',
  mention: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M20 12a8 8 0 1 0-3.562 6.657l1.11 1.664A9.953 9.953 0 0 1 12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10v1.5a3.5 3.5 0 0 1-6.396 1.966A5 5 0 1 1 15 8H17v5.5a1.5 1.5 0 0 0 3 0V12zm-8-3a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></g></svg>',
  orderedList: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M8 4h13v2H8V4zM5 3v3h1v1H3V6h1V4H3V3h2zM3 14v-2.5h2V11H3v-1h3v2.5H4v.5h2v1H3zm2 5.5H3v-1h2V18H3v-1h3v4H3v-1h2v-.5zM8 11h13v2H8v-2zm0 7h13v2H8v-2z"/></g></svg>',
  paragraph: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M12 6v15h-2v-5a6 6 0 1 1 0-12h10v2h-3v15h-2V6h-3zm-2 0a4 4 0 1 0 0 8V6z"/></g></svg>',
  table: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M4 8h16V5H4v3zm10 11v-9h-4v9h4zm2 0h4v-9h-4v9zm-8 0v-9H4v9h4zM3 3h18a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/></g></svg>',
  tableRow: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0H24V24H0z" fill="none" /><path fill="currentColor" d="M12 13c2.761 0 5 2.239 5 5s-2.239 5-5 5-5-2.239-5-5 2.239-5 5-5zm1 2h-2v1.999L9 17v2l2-.001V21h2v-2.001L15 19v-2l-2-.001V15zm7-12c.552 0 1 .448 1 1v6c0 .552-.448 1-1 1H4c-.552 0-1-.448-1-1V4c0-.552.448-1 1-1h16zM5 5v4h14V5H5z"/></g></svg>',
  tableCell: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M21 3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h18zm-1 13H4v3h16v-3zM8 5H4v9h4V5zm6 0h-4v9h4V5zm6 0h-4v9h4V5z"fill-rule=nonzero /></g></svg>',
  taskList: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M11 4h10v2H11V4zm0 4h6v2h-6V8zm0 6h10v2H11v-2zm0 4h6v2h-6v-2zM3 4h6v6H3V4zm2 2v2h2V6H5zm-2 8h6v6H3v-6zm2 2v2h2v-2H5z"/></g></svg>',
  taskItem: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M21 2.992v18.016a1 1 0 0 1-.993.992H3.993A.993.993 0 0 1 3 21.008V2.992A1 1 0 0 1 3.993 2h16.014c.548 0 .993.444.993.992zM19 4H5v16h14V4zm-7.707 9.121l4.243-4.242 1.414 1.414-5.657 5.657-3.89-3.89 1.415-1.414 2.475 2.475z"/></g></svg>',
  text: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M13 6v15h-2V6H5V4h14v2z"/></g></svg>',
  youTube: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M19.606 6.995c-.076-.298-.292-.523-.539-.592C18.63 6.28 16.5 6 12 6s-6.628.28-7.069.403c-.244.068-.46.293-.537.592C4.285 7.419 4 9.196 4 12s.285 4.58.394 5.006c.076.297.292.522.538.59C5.372 17.72 7.5 18 12 18s6.629-.28 7.069-.403c.244-.068.46-.293.537-.592C19.715 16.581 20 14.8 20 12s-.285-4.58-.394-5.005zm1.937-.497C22 8.28 22 12 22 12s0 3.72-.457 5.502c-.254.985-.997 1.76-1.938 2.022C17.896 20 12 20 12 20s-5.893 0-7.605-.476c-.945-.266-1.687-1.04-1.938-2.022C2 15.72 2 12 2 12s0-3.72.457-5.502c.254-.985.997-1.76 1.938-2.022C6.107 4 12 4 12 4s5.896 0 7.605.476c.945.266 1.687 1.04 1.938 2.022zM10 15.5v-7l6 3.5-6 3.5z"fill-rule=nonzero /></g></svg>',
  clearNodes: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M12.651 14.065L11.605 20H9.574l1.35-7.661-7.41-7.41L4.93 3.515 20.485 19.07l-1.414 1.414-6.42-6.42zm-.878-6.535l.27-1.53h-1.8l-2-2H20v2h-5.927L13.5 9.257 11.773 7.53z"/></g></svg>',
  undo: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M18.172 7H11a6 6 0 1 0 0 12h9v2h-9a8 8 0 1 1 0-16h7.172l-2.536-2.536L17.05 1.05 22 6l-4.95 4.95-1.414-1.414L18.172 7z"/></g></svg>',
  redo: '<svg viewBox="0 0 24 24"xmlns=http://www.w3.org/2000/svg><g><path d="M0 0h24v24H0z" fill="none" /><path fill="currentColor" d="M5.828 7l2.536 2.536L6.95 10.95 2 6l4.95-4.95 1.414 1.414L5.828 5H13a8 8 0 1 1 0 16H4v-2h9a6 6 0 1 0 0-12H5.828z"/></g></svg>',
}

type Mark = 'bold' | 'italic' | 'strike' | 'code' | 'highlight' | 'link' | 'subscript' | 'superscript' | 'textstyle' | 'underline'
type Node = 'blockquote' | 'bulletList' | 'codeBlock' | 'document' | 'emoji' | 'hardBreak' | 'hashtag' | 'heading' | 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6' | 'horizontalRule' | 'image' | 'mention' | 'orderedList' | 'paragraph' | 'table' | 'tableRow' | 'tableCell' | 'taskList' | 'taskItem' | 'text' | 'youTube'
type Extra = 'clearNodes' | 'undo' | 'redo' | 'separator'
export type Command = Mark | Node | Extra
export type Commandable = {
  [key in Command]: ChainedCommands | undefined
}

export interface ActionButton {
  /**
   * Name for action
   */
  title: string
  /**
   * Shortcut key for the button
   * From: https://tiptap.dev/api/keyboard-shortcuts
   * And markdown shortcuts: https://tiptap.dev/examples/markdown-shortcuts
   * And typography: https://tiptap.dev/api/extensions/typography
   */
  hotkey?: string
  /**
   * Optional SVG icon
   */
  icon?: string
  /**
   * Command name
   */
  command: Command
  /**
   * Extra parameters
   */
  params?: {
    level?: number
  }
  isStarterKit?: boolean
  isPro?: boolean
  onlyTitle?: boolean
  type?: 'mark' | 'node' | 'extra'
}

interface IMarks {
  bold: ActionButton // starterkit
  italic: ActionButton // starterkit
  strike: ActionButton // starterkit
  code: ActionButton // starterkit
  highlight: ActionButton // @tiptap/extension-highlight
  link: ActionButton // @tiptap/extension-link
  subscript: ActionButton // @tiptap/extension-subscript
  superscript: ActionButton // @tiptap/extension-superscript
  textstyle: ActionButton // starterkit
  underline: ActionButton // @tiptap/extension-underline
}

interface INodes {
  blockquote: ActionButton // starterkit
  bulletList: ActionButton // starterkit
  codeBlock: ActionButton // starterkit
  document: ActionButton // starterkit
  emoji: ActionButton // @tiptap-pro/extension-emoji
  hardBreak: ActionButton // starterkit
  hashtag: ActionButton // soon
  heading: ActionButton // starterkit
  h1: ActionButton // starterkit
  h2: ActionButton // starterkit
  h3: ActionButton // starterkit
  h4: ActionButton // starterkit
  h5: ActionButton // starterkit
  h6: ActionButton // starterkit
  horizontalRule: ActionButton // starterkit
  // listItem: ActionButton // starterkit
  image: ActionButton // @tiptap/extension-image
  mention: ActionButton // starterkit
  orderedList: ActionButton // starterkit
  paragraph: ActionButton // starterkit
  table: ActionButton // @tiptap/extension-table @tiptap/extension-table-row @tiptap/extension-table-header @tiptap/extension-table-cell
  tableRow: ActionButton // @tiptap/extension-table @tiptap/extension-table-row @tiptap/extension-table-header @tiptap/extension-table-cell
  tableCell: ActionButton // @tiptap/extension-table @tiptap/extension-table-row @tiptap/extension-table-header @tiptap/extension-table-cell
  taskList: ActionButton // @tiptap/extension-task-list @tiptap/extension-task-item
  taskItem: ActionButton // @tiptap/extension-task-list @tiptap/extension-task-item
  text: ActionButton // starterkit
  youTube: ActionButton // starterkit
}

interface IExtras {
  clearNodes: ActionButton // starterkit
  undo: ActionButton // starterkit
  redo: ActionButton // starterkit
  separator: ActionButton // starterkit
}

export const Marks: IMarks = {
  bold: {
    icon: icons.bold,
    title: 'Bold',
    hotkey: 'Ctrl+B',
    command: 'bold',
    isStarterKit: true,
    type: 'mark',
  },
  italic: {
    icon: icons.italic,
    title: 'Italic',
    hotkey: 'Ctrl+I',
    command: 'italic',
    isStarterKit: true,
    type: 'mark',
  },
  strike: {
    icon: icons.strike,
    title: 'Strike',
    hotkey: 'Ctrl+Shift+X',
    command: 'strike',
    isStarterKit: true,
    type: 'mark',
  },
  code: {
    icon: icons.code,
    title: 'Code',
    hotkey: 'Ctrl+E',
    command: 'code',
    isStarterKit: true,
    type: 'mark',
  },
  highlight: {
    icon: icons.highlight,
    title: 'Code',
    hotkey: 'Ctrl+Shift+H',
    command: 'highlight',
    type: 'mark',
  },
  link: {
    icon: icons.link,
    title: 'Link',
    hotkey: 'Ctrl+E',
    command: 'link',
    type: 'mark',
  },
  subscript: {
    icon: icons.subscript,
    title: 'Subscript',
    hotkey: 'Ctrl+E',
    command: 'subscript',
    type: 'mark',
  },
  superscript: {
    icon: icons.superscript,
    title: 'Superscript',
    hotkey: 'Ctrl+E',
    command: 'superscript',
    type: 'mark',
  },
  textstyle: {
    icon: icons.textstyle,
    title: 'Textstyle',
    hotkey: 'Ctrl+E',
    command: 'textstyle',
    type: 'mark',
  },
  underline: {
    icon: icons.underline,
    title: 'Underline',
    hotkey: 'Ctrl+U',
    command: 'underline',
    type: 'mark',
  },
}

export const Nodes: INodes = {
  blockquote: {
    icon: icons.blockquote,
    title: 'Blockquote',
    hotkey: 'Ctrl+E',
    command: 'blockquote',
    isStarterKit: true,
    type: 'node',
  },
  bulletList: {
    icon: icons.bulletList,
    title: 'Bullet list',
    hotkey: 'Ctrl+E',
    command: 'bulletList',
    isStarterKit: true,
    type: 'node',
  },
  codeBlock: {
    icon: icons.codeBlock,
    title: 'Code block',
    hotkey: 'Ctrl+E',
    command: 'codeBlock',
    isStarterKit: true,
    type: 'node',
  },
  document: {
    icon: icons.document,
    title: 'Document',
    hotkey: 'Ctrl+E',
    command: 'document',
    isStarterKit: true,
    type: 'node',
  },
  emoji: {
    icon: icons.emoji,
    title: 'Emoji',
    hotkey: 'Ctrl+E',
    command: 'emoji',
    isPro: true,
    type: 'node',
  },
  hardBreak: {
    icon: icons.hardBreak,
    title: 'Hard break',
    hotkey: 'Ctrl+E',
    command: 'hardBreak',
    isStarterKit: true,
    type: 'node',
  },
  hashtag: {
    icon: icons.hashtag,
    title: 'Hashtag',
    hotkey: 'Ctrl+E',
    command: 'hashtag',
    type: 'node',
  },
  heading: {
    icon: icons.heading,
    title: 'Heading',
    hotkey: 'Ctrl+E',
    command: 'heading',
    isStarterKit: true,
    type: 'node',
  },
  h1: {
    icon: icons.h1,
    title: 'Heading',
    hotkey: 'Ctrl+Alt+1',
    command: 'heading',
    params: {
      level: 1,
    },
    isStarterKit: true,
    type: 'node',
  },
  h2: {
    icon: icons.h2,
    title: 'Heading',
    hotkey: 'Ctrl+Alt+2',
    command: 'heading',
    params: {
      level: 2,
    },
    isStarterKit: true,
    type: 'node',
  },
  h3: {
    icon: icons.h3,
    title: 'Heading',
    hotkey: 'Ctrl+Alt+3',
    command: 'heading',
    params: {
      level: 3,
    },
    isStarterKit: true,
    type: 'node',
  },
  h4: {
    icon: icons.h4,
    title: 'Heading',
    hotkey: 'Ctrl+Alt+4',
    command: 'heading',
    params: {
      level: 4,
    },
    isStarterKit: true,
    type: 'node',
  },
  h5: {
    icon: icons.h5,
    title: 'Heading',
    hotkey: 'Ctrl+Alt+5',
    command: 'heading',
    params: {
      level: 5,
    },
    isStarterKit: true,
    type: 'node',
  },
  h6: {
    icon: icons.h6,
    title: 'Heading',
    hotkey: 'Ctrl+Alt+6',
    command: 'heading',
    params: {
      level: 6,
    },
    isStarterKit: true,
    type: 'node',
  },
  horizontalRule: {
    icon: icons.horizontalRule,
    title: 'Horizontal rule',
    hotkey: 'Ctrl+E',
    command: 'horizontalRule',
    isStarterKit: true,
    type: 'node',
  },
  image: {
    icon: icons.image,
    title: 'Image',
    hotkey: 'Ctrl+E',
    command: 'image',
    type: 'node',
  },
  mention: {
    icon: icons.mention,
    title: 'Mention',
    hotkey: 'Ctrl+E',
    command: 'mention',
    isStarterKit: true,
    type: 'node',
  },
  orderedList: {
    icon: icons.orderedList,
    title: 'Ordered list',
    hotkey: 'Ctrl+Shift+7',
    command: 'orderedList',
    isStarterKit: true,
    type: 'node',
  },
  paragraph: {
    icon: icons.bold,
    title: 'Paragraph',
    hotkey: 'Ctrl+E',
    command: 'paragraph',
    isStarterKit: true,
    type: 'node',
  },
  table: {
    icon: icons.table,
    title: 'Table',
    hotkey: 'Ctrl+E',
    command: 'table',
    type: 'node',
  },
  tableRow: {
    icon: icons.tableRow,
    title: 'tableRow',
    hotkey: 'Ctrl+E',
    command: 'tableRow',
    type: 'node',
  },
  tableCell: {
    icon: icons.tableCell,
    title: 'tableCell',
    hotkey: 'Ctrl+E',
    command: 'tableCell',
    type: 'node',
  },
  taskList: {
    icon: icons.taskList,
    title: 'taskList',
    hotkey: 'Ctrl+E',
    command: 'taskList',
    type: 'node',
  },
  taskItem: {
    icon: icons.taskItem,
    title: 'taskItem',
    hotkey: 'Ctrl+E',
    command: 'taskItem',
    type: 'node',
  },
  text: {
    icon: icons.text,
    title: 'Text',
    hotkey: 'Ctrl+E',
    command: 'text',
    isStarterKit: true,
    type: 'node',
  },
  youTube: {
    icon: icons.youTube,
    title: 'youTube',
    hotkey: 'Ctrl+E',
    command: 'youTube',
    isStarterKit: true,
    type: 'node',
  },
}

export const Extras: IExtras = {
  clearNodes: {
    icon: icons.clearNodes,
    title: 'Clear format',
    command: 'clearNodes',
    isStarterKit: true,
    type: 'extra',
  },
  undo: {
    icon: icons.undo,
    title: 'Undo',
    hotkey: 'Ctrl+Z',
    command: 'undo',
    isStarterKit: true,
    type: 'extra',
  },
  redo: {
    icon: icons.redo,
    title: 'Redo',
    hotkey: 'Ctrl+Y',
    command: 'redo',
    isStarterKit: true,
    type: 'extra',
  },
  separator: {
    title: '|',
    command: 'separator',
    isStarterKit: true,
    type: 'extra',
  },
}

// const addImage = (editor: Editor) => {
//   const url = window.prompt('URL')

//   if (url)
//     editor.chain().focus().setImage({ src: url }).run()
// }

// const addVideo = (editor: Editor, width = '640', height = '480') => {
//   const url = prompt('Enter YouTube URL')

//   editor.commands.setYoutubeVideo({
//     src: url,
//     width: Math.max(320, parseInt(width, 10)) || 640,
//     height: Math.max(180, parseInt(height, 10)) || 480,
//   })
// }

const setLink = (editor: EditorT) => {
  const previousUrl = editor.getAttributes('link').href
  // eslint-disable-next-line no-alert
  const url = window.prompt('URL', previousUrl)

  // cancelled
  if (url === null)
    return

  // empty
  if (url === '') {
    editor
      .chain()
      .focus()
      .extendMarkRange('link')
      .unsetLink()
      .run()
  }

  editor
    .chain()
    .focus()
    .extendMarkRange('link')
    .setLink({ href: url })
    .run()
}

export const ExecuteCommand = (editor: EditorT, action: ActionButton) => {
  switch (action.command) {
    case 'bold':
      // https://tiptap.dev/api/marks/bold
      editor.chain().toggleBold().focus().run()
      break
    case 'italic':
      // https://tiptap.dev/api/marks/italic
      editor.chain().toggleItalic().focus().run()
      break
    case 'strike':
      // https://tiptap.dev/api/marks/strike
      editor.chain().toggleStrike().focus().run()
      break
    case 'code':
      // https://tiptap.dev/api/marks/code
      editor.chain().toggleCode().focus().run()
      break
    case 'highlight':
      // https://tiptap.dev/api/marks/highlight
      // editor.commands.setHighlight()
      break
    case 'link':
      // https://tiptap.dev/api/marks/link
      setLink(editor)
      break
    case 'subscript':
      // https://tiptap.dev/api/marks/subscript
      // editor.commands.setSubscript()
      break
    case 'superscript':
      // https://tiptap.dev/api/marks/superscript
      // editor.commands.setSuperscript()
      break
    case 'textstyle':
      break
    case 'underline':
      // https://tiptap.dev/api/marks/underline
      // editor.commands.setUnderline()
      break
    case 'blockquote':
      // https://tiptap.dev/api/nodes/blockquote
      editor.chain().focus().toggleBlockquote().run()
      break
    case 'bulletList':
      // https://tiptap.dev/api/nodes/bullet-list
      editor.chain().focus().toggleBulletList().run()
      break
    case 'codeBlock':
      // https://tiptap.dev/api/nodes/code-block
      editor.chain().focus().toggleCodeBlock().run()
      break
    case 'document':
      // https://tiptap.dev/api/nodes/document
      break
    case 'emoji':
      // https://tiptap.dev/api/nodes/emoji
      break
    case 'hardBreak':
      // https://tiptap.dev/api/nodes/hard-break
      editor.commands.setHardBreak()
      break
    case 'hashtag':
      // https://tiptap.dev/api/nodes/hashtag
      break
    case 'heading':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 2 })
      break
    case 'h1':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 1 })
      break
    case 'h2':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 2 })
      break
    case 'h3':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 3 })
      break
    case 'h4':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 4 })
      break
    case 'h5':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 5 })
      break
    case 'h6':
      // https://tiptap.dev/api/nodes/heading
      editor.commands.toggleHeading({ level: 6 })
      break
    case 'horizontalRule':
      // https://tiptap.dev/api/nodes/horizontal-rule
      editor.commands.setHorizontalRule()
      break
    case 'image':
      // https://tiptap.dev/api/nodes/image
      /**
       * Install `@tiptap/extension-image`
       * Add to `extensions`
       */
      // addImage(editor)
      break
    case 'mention':
      // https://tiptap.dev/api/nodes/mention
      break
    case 'orderedList':
      // https://tiptap.dev/api/nodes/ordered-list
      editor.commands.toggleOrderedList()
      break
    case 'paragraph':
      // https://tiptap.dev/api/nodes/paragraph
      editor.commands.setParagraph()
      break
    case 'table':
      // https://tiptap.dev/api/nodes/table
      break
    case 'tableRow':
      // https://tiptap.dev/api/nodes/table-row
      break
    case 'tableCell':
      // https://tiptap.dev/api/nodes/table-cell
      break
    case 'taskList':
      // https://tiptap.dev/api/nodes/task-list
      /**
       * Install `@tiptap/extension-task-list @tiptap/extension-task-item`
       */
      // editor.commands.toggleTaskList()
      break
    case 'taskItem':
      // https://tiptap.dev/api/nodes/task-item
      break
    case 'text':
      // https://tiptap.dev/api/nodes/text
      break
    case 'youTube':
      // https://tiptap.dev/api/nodes/youtube
      // addVideo(editor)
      break
    case 'clearNodes':
      // https://tiptap.dev/api/commands/clear-nodes
      editor.commands.clearNodes()
      break
    case 'undo':
      // https://tiptap.dev/api/extensions/history
      editor.chain().focus().undo().run()
      break
    case 'redo':
      // https://tiptap.dev/api/extensions/history
      editor.chain().focus().redo().run()
      break
    case 'separator':
      break
    default:
      break
  }
}
