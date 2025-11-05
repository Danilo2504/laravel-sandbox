import { BootboxAlertAdapter, CommonAdapter, EditorHTML, NumbersOnly, PasswordToggler } from "./Stardust/Adapters";
import { StardustOrchestrator } from "./Stardust/Orchestrator";

const Stardust = new StardustOrchestrator(false);

Stardust.registerAdapter('numbersOnly', NumbersOnly);
Stardust.registerAdapter('passwordToggler', PasswordToggler);
Stardust.registerAdapter('editorHtml', EditorHTML);
Stardust.registerAdapter('commonAdapter', CommonAdapter);
Stardust.registerAdapter('bootboxAlert', BootboxAlertAdapter);

export default Stardust;