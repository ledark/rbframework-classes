. D:\Path-Variable-Functions.ps1

Function Run-Testing() {
	Use-Php85
	$CurrentDirectory = Set-EscapeCharacters $PSScriptRoot
	Run-Tests $CurrentDirectory
}

$WhileState = $true
While ($WhileState) {

    Run-Testing
	Write-Host -ForegroundColor Yellow $CurrentDirectory
	
    $options = [System.Management.Automation.Host.ChoiceDescription[]]("y", "n")
    $title = Set-EscapeCharacters $PSScriptRoot
    $message = "Continuar?"
    $result = $host.ui.PromptForChoice($title, $message, $options, 1)

    if ($result -eq 0) {
        $choice = "Re-executando...."
    }
    elseif ($result -eq 1) {
        $WhileState = $false
    }

}
Write-Host -ForegroundColor Yellow $CurrentDirectory
#Read-Host -Prompt ('Aperte qualquer tecla para sair.')