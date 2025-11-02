import { CommonAdapter, EditorHTML, NumbersOnly, PasswordToggler } from "./Stardust/Adapters";
import { StardustOrchestrator } from "./Stardust/Orchestrator";

const Stardust = new StardustOrchestrator();

Stardust.registerAdapter('numbersOnly', NumbersOnly);
Stardust.registerAdapter('passwordToggler', PasswordToggler);
Stardust.registerAdapter('editorHtml', EditorHTML);
Stardust.registerAdapter('commonAdapter', CommonAdapter);

export default Stardust;